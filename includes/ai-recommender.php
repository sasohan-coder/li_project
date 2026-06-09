<?php

/**
 * স্মার্ট বই সুপারিশ ইঞ্জিন - এআই
 */
class BookRecommender {
    private $db;
    private $config;
    private $openLibrary;
    
    public function __construct($db, $config) {
        $this->db = $db;
        $this->config = $config;
        
        // OpenLibrary API লোড করুন
        require_once __DIR__ . '/openlibrary-api.php';
        $this->openLibrary = new OpenLibraryAPI();
        
        // নিশ্চিত করুন টেবিলগুলো আছে
        $this->ensureRatingTables();
    }
    
    /**
     * শিক্ষার্থীর জন্য সুপারিশকৃত বই পান
     */
    public function getRecommendations($studentEmail, $limit = 5) {
        if (!$this->config['ai_recommendation_enabled']) {
            return [];
        }
        
        // ১. সহযোগী ফিল্টারিং থেকে সুপারিশ
        $collaborativeRecs = $this->getCollaborativeRecommendations($studentEmail, $limit);
        
        // ২. কন্টেন্ট-ভিত্তিক সুপারিশ
        $contentRecs = $this->getContentBasedRecommendations($studentEmail, $limit);
        
        // ৩. সংমিশ্রণ এবং স্কোর করুন
        $merged = $this->mergeAndScoreRecommendations($collaborativeRecs, $contentRecs, $limit);
        
        // ৪. অতিরিক্ত তথ্য সংযুক্ত করুন
        return $this->enrichRecommendations($merged);
    }
    
    /**
     * সহযোগী ফিল্টারিং - অনুরূপ স্বাদের ব্যবহারকারীদের পছন্দ থেকে
     */
    private function getCollaborativeRecommendations($studentEmail, $limit) {
        $limit = (int) $limit;
        $sql = "SELECT 
                    b.book_name,
                    b.author_name,
                    b.book_image,
                    COUNT(br2.id) as match_count,
                    AVG(br2.rating) as avg_rating,
                    'collaborative' as source,
                    'অনুরূপ আগ্রহের শিক্ষার্থীরা পছন্দ করেছে' as reason
                FROM book_ratings br1
                INNER JOIN book_ratings br2 ON br1.book_name = br2.book_name AND br1.rating >= ?
                INNER JOIN books b ON br2.book_name = b.book_name
                WHERE br1.student_email = ?
                AND br2.student_email != ?
                AND br2.rating >= ?
                AND b.book_name NOT IN (
                    SELECT book_name FROM book_ratings WHERE student_email = ?
                )
                GROUP BY b.book_name, b.author_name, b.book_image
                ORDER BY match_count DESC, avg_rating DESC
                LIMIT " . $limit;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $this->config['min_rating_threshold'],
                $studentEmail,
                $studentEmail,
                $this->config['min_rating_threshold'],
                $studentEmail
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Collaborative filtering error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * কন্টেন্ট-ভিত্তিক সুপারিশ - একই ধরনের বই খুঁজুন
     */
    private function getContentBasedRecommendations($studentEmail, $limit) {
        $limit = (int) $limit;
        // শিক্ষার্থী যা পড়েছে তার ঘরানা খুঁজুন
        $sql = "SELECT 
                    bm.book_name,
                    b.author_name,
                    b.book_image,
                    bm.genre,
                    bm.popularity_score,
                    'content' as source,
                    'আপনার পড়া একই ঘরানার বই' as reason
                FROM book_metadata bm
                INNER JOIN books b ON bm.book_name = b.book_name
                WHERE bm.genre IN (
                    SELECT DISTINCT bm2.genre 
                    FROM book_metadata bm2
                    INNER JOIN book_ratings br ON bm2.book_name = br.book_name
                    WHERE br.student_email = ? AND br.rating >= ?
                )
                AND bm.book_name NOT IN (
                    SELECT book_name FROM book_ratings WHERE student_email = ?
                )
                AND bm.popularity_score > 0
                ORDER BY bm.popularity_score DESC, bm.avg_rating DESC
                LIMIT " . $limit;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $studentEmail,
                $this->config['min_rating_threshold'],
                $studentEmail
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Content-based filtering error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * সহযোগী এবং কন্টেন্ট সুপারিশ মার্জ করুন
     */
    private function mergeAndScoreRecommendations($collab, $content, $limit) {
        $merged = [];
        $seen = [];
        
        // সহযোগী ফিল্টারিং উচ্চ ওজন
        foreach ($collab as $rec) {
            $score = ($rec['match_count'] ?? 0) * $this->config['collaboration_boost'];
            $score += ($rec['avg_rating'] ?? 0) * 10;
            
            $merged[$rec['book_name']] = array_merge($rec, ['score' => $score]);
            $seen[$rec['book_name']] = true;
        }
        
        // কন্টেন্ট সুপারিশ কম ওজন
        foreach ($content as $rec) {
            if (!isset($seen[$rec['book_name']])) {
                $score = ($rec['popularity_score'] ?? 0) * 5;
                $merged[$rec['book_name']] = array_merge($rec, ['score' => $score]);
            }
        }
        
        // স্কোর দ্বারা সাজান
        usort($merged, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($merged, 0, $limit);
    }
    
    /**
     * অতিরিক্ত তথ্য সংযুক্ত করুন
     */
    private function enrichRecommendations($recommendations) {
        foreach ($recommendations as &$rec) {
            // রেটিং গণনা যোগ করুন
            $ratingStmt = $this->db->prepare(
                "SELECT AVG(rating) as avg, COUNT(*) as count FROM book_ratings WHERE book_name = ?"
            );
            $ratingStmt->execute([$rec['book_name']]);
            $ratingData = $ratingStmt->fetch(PDO::FETCH_ASSOC);
            
            $rec['community_rating'] = round((float) ($ratingData['avg'] ?? 0), 1);
            $rec['rating_count'] = (int) ($ratingData['count'] ?? 0);
            
            // উপলব্ধতা পরীক্ষা করুন
            $bookStmt = $this->db->prepare(
                "SELECT available_quantity FROM books WHERE book_name = ?"
            );
            $bookStmt->execute([$rec['book_name']]);
            $bookData = $bookStmt->fetch(PDO::FETCH_ASSOC);
            
            $rec['available'] = ((int) ($bookData['available_quantity'] ?? 0)) > 0;
            $rec['available_quantity'] = (int) ($bookData['available_quantity'] ?? 0);
            
            // OpenLibrary থেকে বই তথ্য পান
            if ($this->config['enable_openlibrary'] ?? false) {
                $author = isset($rec['author_name']) ? $rec['author_name'] : '';
                
                $olData = $this->openLibrary->getBookInfo($rec['book_name'], $author);
                if ($olData) {
                    $rec['description'] = $olData['description'] ?? '';
                    $rec['isbn'] = $olData['isbn'] ?? '';
                    $rec['pages'] = $olData['pages'] ?? null;
                    $rec['global_rating'] = $olData['rating_average'] ?? null;
                    $rec['global_ratings_count'] = $olData['ratings_count'] ?? 0;
                    $rec['first_publish_year'] = $olData['first_publish_year'] ?? '';
                    $rec['cover_url'] = $olData['cover_url'] ?? null;
                    $rec['publishers'] = $olData['publishers'] ?? [];
                } else {
                    $rec['description'] = '';
                    $rec['cover_url'] = null;
                }
            }
        }
        
        return $recommendations;
    }
    
    /**
     * রেটিং রেকর্ড করুন বা আপডেট করুন
     */
    public function addOrUpdateRating($studentEmail, $bookName, $rating, $review = '') {
        try {
            $sql = "INSERT INTO book_ratings (student_email, book_name, rating, review) 
                    VALUES (?, ?, ?, ?)
                    ON CONFLICT (student_email, book_name) DO UPDATE SET 
                    rating = EXCLUDED.rating, review = EXCLUDED.review, updated_at = CURRENT_TIMESTAMP";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $studentEmail,
                $bookName,
                $rating,
                $review
            ]);
            
            // মেটাডেটা আপডেট করুন
            $this->updateBookMetadata($bookName);
            
            return true;
        } catch (Exception $e) {
            error_log("Rating error: " . $e->getMessage());
            return false;
        }
    }
    
    private function ensureRatingTables(): void
    {
        try {
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS book_ratings (
                    id SERIAL PRIMARY KEY,
                    student_email VARCHAR(120) NOT NULL,
                    book_name VARCHAR(150) NOT NULL,
                    rating INT NOT NULL DEFAULT 0,
                    review TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    CONSTRAINT unique_rating UNIQUE (student_email, book_name)
                )"
            );
        } catch (Exception $e) {
            error_log("Error creating book_ratings: " . $e->getMessage());
        }

        try {
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS book_metadata (
                    book_name VARCHAR(150) PRIMARY KEY,
                    genre VARCHAR(100) DEFAULT '',
                    difficulty_level INT DEFAULT 3,
                    popularity_score FLOAT DEFAULT 0,
                    total_ratings INT DEFAULT 0,
                    avg_rating FLOAT DEFAULT 0,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )"
            );
        } catch (Exception $e) {
            error_log("Error creating book_metadata: " . $e->getMessage());
        }
    }
    
    /**
     * বইয়ের মেটাডেটা আপডেট করুন
     */
    private function updateBookMetadata($bookName) {
        try {
            $insertStmt = $this->db->prepare("INSERT INTO book_metadata (book_name) VALUES (?) ON CONFLICT DO NOTHING");
            $insertStmt->execute([$bookName]);

            $sql = "UPDATE book_metadata 
                    SET avg_rating = (SELECT AVG(rating) FROM book_ratings WHERE book_name = ?),
                        total_ratings = (SELECT COUNT(*) FROM book_ratings WHERE book_name = ? AND rating > 0),
                        popularity_score = (SELECT COUNT(*) FROM book_ratings WHERE book_name = ? AND rating >= ?) * 1.5
                    WHERE book_name = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $bookName,
                $bookName,
                $bookName,
                $this->config['min_rating_threshold'],
                $bookName
            ]);
        } catch (Exception $e) {
            error_log("Metadata update error: " . $e->getMessage());
        }
    }
    
    /**
     * নতুন বইয়ের জন্য মেটাডেটা তৈরি করুন
     */
    public function createBookMetadata($bookName, $genre = '', $difficulty = 3) {
        try {
            $sql = "INSERT INTO book_metadata (book_name, genre, difficulty_level) 
                    VALUES (?, ?, ?)
                    ON CONFLICT (book_name) DO UPDATE SET 
                    genre = EXCLUDED.genre, difficulty_level = EXCLUDED.difficulty_level";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$bookName, $genre, $difficulty]);
            return true;
        } catch (Exception $e) {
            error_log("Metadata creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * জনপ্রিয় বই পান (কোনো ইতিহাস ছাড়া নতুন ব্যবহারকারীদের জন্য)
     */
    public function getPopularBooks($limit = 5) {
        $limit = (int) $limit;
        $sql = "SELECT 
                    b.book_name,
                    b.author_name,
                    b.book_image,
                    b.publication_name,
                    bm.popularity_score,
                    bm.avg_rating,
                    bm.total_ratings,
                    'popular' as source,
                    'এখন জনপ্রিয়' as reason
                FROM books b
                LEFT JOIN book_metadata bm ON b.book_name = bm.book_name
                ORDER BY COALESCE(bm.popularity_score, 0) DESC, COALESCE(bm.avg_rating, 0) DESC
                LIMIT " . $limit;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->enrichRecommendations($results);
        } catch (Exception $e) {
            error_log("Popular books error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * সিস্টেম উষ্ণ করুন - নতুন বইয়ের জন্য মেটাডেটা প্রাথমিক করুন
     */
    public function warmupSystem() {
        try {
            $sql = "INSERT INTO book_metadata (book_name, genre, difficulty_level)
                    SELECT book_name, '', 3 FROM books
                    WHERE book_name NOT IN (SELECT book_name FROM book_metadata)
                    ON CONFLICT DO NOTHING";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Warmup error: " . $e->getMessage());
            return false;
        }
    }
}
?>
