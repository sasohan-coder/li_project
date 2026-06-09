<?php

/**
 * OpenLibrary API Integration
 * বিনামূল্যে বই তথ্য API
 */
class OpenLibraryAPI {
    private $baseUrl = 'https://openlibrary.org/api/books';
    private $baseSearchUrl = 'https://openlibrary.org/search.json';
    private $coverUrl = 'https://covers.openlibrary.org/b';
    private $timeout = 5;
    private $cacheFile;
    private $cache = [];

    public function __construct() {
        $this->cacheFile = sys_get_temp_dir() . '/ol_cache.json';
        if (file_exists($this->cacheFile)) {
            $this->cache = json_decode(file_get_contents($this->cacheFile), true) ?? [];
        }
    }

    private function getFromCache($key) {
        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }

    private function saveToCache($key, $data) {
        $this->cache[$key] = $data;
        file_put_contents($this->cacheFile, json_encode($this->cache));
    }
    
    /**
     * ISBN বা বই নাম দিয়ে বই খুঁজুন
     */
    public function searchBook($query) {
        $cacheKey = 'search_' . md5($query);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached ?: null;
        }

        try {
            $url = $this->baseSearchUrl . '?title=' . urlencode($query) . '&limit=1';
            $result = $this->fetchUrl($url);
            
            if ($result && isset($result['docs']) && count($result['docs']) > 0) {
                $book = $result['docs'][0];
                $parsed = $this->parseSearchResult($book);
                $this->saveToCache($cacheKey, $parsed);
                return $parsed;
            }
            $this->saveToCache($cacheKey, false);
            return null;
        } catch (Exception $e) {
            error_log("OpenLibrary search error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * ISBN দিয়ে বই খোঁজা
     */
    public function getBookByISBN($isbn) {
        $isbn = preg_replace('/[^0-9X]/', '', strtoupper($isbn));
        $cacheKey = 'isbn_' . $isbn;
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached ?: null;
        }

        try {
            $url = $this->baseUrl . '?bibkeys=ISBN:' . $isbn . '&jscmd=details&format=json';
            $result = $this->fetchUrl($url);
            
            if ($result) {
                $key = 'ISBN:' . $isbn;
                if (isset($result[$key])) {
                    $parsed = $this->parseBookDetails($result[$key]);
                    $this->saveToCache($cacheKey, $parsed);
                    return $parsed;
                }
            }
            $this->saveToCache($cacheKey, false);
            return null;
        } catch (Exception $e) {
            error_log("OpenLibrary ISBN lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * কভার ইমেজ URL পান
     */
    public function getCoverUrl($bookKey, $size = 'M') {
        $id = str_replace('/books/OL', '', $bookKey);
        $id = str_replace('M', '', $id);
        return $this->coverUrl . '/id/' . $id . '-' . $size . '.jpg';
    }
    
    /**
     * বই নাম থেকে কভার ইমেজ পান
     */
    public function getCoverByTitle($title, $author = '', $size = 'M') {
        $cacheKey = 'cover_' . md5($title . '_' . $author . '_' . $size);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached ?: null;
        }

        try {
            $url = $this->baseSearchUrl . '?title=' . urlencode($title);
            if (!empty($author)) {
                $url .= '&author=' . urlencode($author);
            }
            $url .= '&limit=1';
            
            $result = $this->fetchUrl($url);
            if ($result && isset($result['docs'][0])) {
                $book = $result['docs'][0];
                if (isset($book['cover_i'])) {
                    $coverUrl = $this->coverUrl . '/id/' . $book['cover_i'] . '-' . $size . '.jpg';
                    $this->saveToCache($cacheKey, $coverUrl);
                    return $coverUrl;
                }
            }
            $this->saveToCache($cacheKey, false);
            return null;
        } catch (Exception $e) {
            error_log("OpenLibrary cover lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * বইয়ের সম্পূর্ণ তথ্য পান (শিরোনাম এবং লেখক দিয়ে)
     */
    public function getBookInfo($title, $author = '') {
        $cacheKey = 'info_' . md5($title . '_' . $author);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached ?: null;
        }

        try {
            $book = $this->searchBook($title);
            if ($book) {
                $book['cover_url'] = $this->getCoverByTitle($title, $author, 'M');
                $this->saveToCache($cacheKey, $book);
                return $book;
            }
            $this->saveToCache($cacheKey, false);
            return null;
        } catch (Exception $e) {
            error_log("OpenLibrary info fetch error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * সার্চ ফলাফল পার্স করুন
     */
    private function parseSearchResult($book) {
        return [
            'title' => $book['title'] ?? '',
            'author' => !empty($book['author_name']) ? $book['author_name'][0] : '',
            'description' => $book['first_sentence'][0] ?? '',
            'first_publish_year' => $book['first_publish_year'] ?? '',
            'isbn' => $book['isbn'][0] ?? '',
            'rating_average' => $book['ratings_average'] ?? null,
            'ratings_count' => $book['ratings_count'] ?? 0,
            'cover_i' => $book['cover_i'] ?? null,
            'key' => $book['key'] ?? '',
            'pages' => $book['number_of_pages_median'] ?? null,
            'publishers' => $book['publisher'] ?? [],
            'languages' => $book['language'] ?? []
        ];
    }
    
    /**
     * বই বিস্তারিত পার্স করুন
     */
    private function parseBookDetails($bookData) {
        $details = $bookData['details'] ?? $bookData;
        return [
            'title' => $details['title'] ?? '',
            'author' => !empty($details['authors']) ? $details['authors'][0]['name'] : '',
            'description' => $details['description'] ?? '',
            'isbn' => $bookData['ISBN:10'][0] ?? $bookData['ISBN:13'][0] ?? '',
            'pages' => $details['number_of_pages'] ?? null,
            'publishers' => $details['publishers'] ?? [],
            'publish_date' => $details['publish_date'] ?? '',
            'key' => $bookData['key'] ?? ''
        ];
    }
    
    /**
     * URL থেকে ডেটা ফেচ করুন (cURL ব্যবহার করে)
     */
    private function fetchUrl($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                return json_decode($response, true);
            }
            return null;
        } catch (Exception $e) {
            error_log("cURL error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * একাধিক বইয়ের তথ্য পান (দ্রুত মোডে)
     */
    public function enrichBooks($books) {
        $enriched = [];
        
        foreach ($books as $book) {
            $bookName = $book['book_name'] ?? '';
            $author = $book['author_name'] ?? '';
            
            $olData = $this->getBookInfo($bookName, $author);
            
            if ($olData) {
                $book['openlibrary_data'] = [
                    'description' => $olData['description'] ?? '',
                    'cover_url' => $olData['cover_url'] ?? null,
                    'isbn' => $olData['isbn'] ?? '',
                    'pages' => $olData['pages'] ?? null,
                    'global_rating' => $olData['rating_average'] ?? null,
                    'ratings_count' => $olData['ratings_count'] ?? 0,
                    'publishers' => $olData['publishers'] ?? [],
                    'first_publish_year' => $olData['first_publish_year'] ?? ''
                ];
            }
            
            $enriched[] = $book;
        }
        
        return $enriched;
    }
}

?>
