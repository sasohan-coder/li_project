<?php

/**
 * Groq API Integration - AI chatbot for the library
 * Fast LLM API
 */
class GroqChatbot {
    private $apiKey;
    private $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model;
    private $timeout = 30;

    public function __construct($apiKey = null, $model = null) {
        // API key from the environment or the caller
        $this->apiKey = $apiKey ?? getenv('GROQ_API_KEY');
        $this->model = $model ?: (getenv('GROQ_MODEL') ?: 'llama-3.3-70b-versatile');

        if (empty($this->apiKey)) {
            throw new Exception("Groq API key not set. Set GROQ_API_KEY environment variable.");
        }
    }
    /**
     * চ্যাট বার্তা পাঠান এবং উত্তর পান
     */
    public function chat($messages, $temperature = 0.7, $maxTokens = 1024) {
        try {
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ];
            
            $response = $this->callAPI($payload);
            
            if (isset($response['choices']) && count($response['choices']) > 0) {
                return [
                    'success' => true,
                    'message' => $response['choices'][0]['message']['content'],
                    'usage' => $response['usage'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'error' => 'No response from API',
                'response' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * লাইব্রেরি-নির্দিষ্ট প্রশ্নের উত্তর দিন
     */
    public function askAboutLibrary($question, $libraryContext = []) {
        // সিস্টেম প্রম্পট তৈরি করুন
        $systemPrompt = $this->buildSystemPrompt($libraryContext);
        
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $question
            ]
        ];
        
        return $this->chat($messages, 0.5);
    }
    
    /**
     * সিস্টেম প্রম্পট তৈরি করুন
     */
    private function buildSystemPrompt($context = []) {
        $systemPrompt = <<<EOT
আপনি একটি লাইব্রেরি ম্যানেজমেন্ট সিস্টেমের সহায়ক চ্যাটবট।

আপনার দায়িত্ব:
1. বই সম্পর্কে প্রশ্নের উত্তর দিন
2. বই খুঁজে পেতে সাহায্য করুন
3. লাইব্রেরি নীতি সম্পর্কে ব্যাখ্যা করুন
4. পাঠকদের জন্য সুপারিশ করুন
5. বন্ধুত্বপূর্ণ এবং সহায়ক হন

ভাষা: বাংলা (যদি প্রশ্ন বাংলায় হয়)

নির্দেশনা:
- সংক্ষিপ্ত এবং পরিষ্কার উত্তর দিন
- যদি জানেন না, তা বলুন
- সর্বদা বিনয়ী থাকুন
- ব্যবহারকারীকে সাহায্য করার চেষ্টা করুন

লাইব্রেরির তথ্য:
EOT;
        
        if (!empty($context['library_name'])) {
            $systemPrompt .= "\n- লাইব্রেরির নাম: " . $context['library_name'];
        }
        
        if (!empty($context['total_books'])) {
            $systemPrompt .= "\n- মোট বই: " . $context['total_books'];
        }
        
        if (!empty($context['opening_hours'])) {
            $systemPrompt .= "\n- খোলার সময়: " . $context['opening_hours'];
        }
        
        return $systemPrompt;
    }
    
    /**
     * একাধিক টার্ন সহ কথোপকথন
     */
    public function conversation($messages) {
        return $this->chat($messages);
    }
    
    /**
     * API কল করুন
     */
    private function callAPI($payload) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                throw new Exception("cURL Error: " . $error);
            }
            
            if ($httpCode !== 200) {
                $errorResponse = json_decode($response, true);
                $errorMsg = $errorResponse['error']['message'] ?? "HTTP $httpCode";
                throw new Exception("Groq API Error: " . $errorMsg);
            }
            
            return json_decode($response, true);
        } catch (Exception $e) {
            error_log("Groq API Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * পাওয়া API কোটা সম্পর্কে তথ্য
     */
    public function getModelInfo() {
        return [
            'model' => $this->model,
            'max_tokens' => 4096,
            'speed' => 'দ্রুততম',
            'free' => true,
            'rateLimit' => '30 requests per minute (আনলিমিটেড মেসেজ পার মাসে)'
        ];
    }
}
?>
