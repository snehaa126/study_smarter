<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'] ?? '';

    if (empty($text)) {
        echo json_encode(["success" => false, "message" => "No text provided"]);
        exit;
    }

    // Improved extractive summarization function
    function improvedSummarize($text, $sentenceCount = 3) {
        // Split text into sentences
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        if (count($sentences) <= $sentenceCount) {
            return $text; // Return original if it's already short
        }
        
        // Calculate sentence scores (improved implementation)
        $scores = [];
        $wordFrequency = [];
        
        // Create a list of stopwords (common words to ignore)
        $stopwords = array('a', 'about', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 
                          'has', 'have', 'in', 'is', 'it', 'of', 'on', 'that', 'the', 'this', 
                          'to', 'was', 'were', 'will', 'with');
        
        // Count word frequency with stopwords removed
        foreach ($sentences as $i => $sentence) {
            $words = preg_split('/\s+/', strtolower($sentence), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($words as $word) {
                // Remove punctuation
                $word = preg_replace('/[^\w\s]/', '', $word);
                
                // Only count if not a stopword and longer than 3 characters
                if (!in_array($word, $stopwords) && strlen($word) > 3) {
                    $wordFrequency[$word] = isset($wordFrequency[$word]) ? $wordFrequency[$word] + 1 : 1;
                }
            }
        }
        
        // Give higher weight to first and last sentences (likely to contain key information)
        $positionWeight = [];
        $sentenceCount = count($sentences);
        for ($i = 0; $i < $sentenceCount; $i++) {
            // First sentence gets highest weight, last sentence second highest
            if ($i == 0) $positionWeight[$i] = 1.5;
            else if ($i == $sentenceCount - 1) $positionWeight[$i] = 1.25;
            // Middle sentences get descending weights
            else $positionWeight[$i] = 1 - (($i / $sentenceCount) * 0.5);
        }
        
        // Score sentences based on word frequency and position
        foreach ($sentences as $i => $sentence) {
            $scores[$i] = 0;
            $words = preg_split('/\s+/', strtolower($sentence), -1, PREG_SPLIT_NO_EMPTY);
            
            // Remove punctuation
            $words = array_map(function($word) {
                return preg_replace('/[^\w\s]/', '', $word);
            }, $words);
            
            // Count words that aren't stopwords
            $relevantWords = 0;
            foreach ($words as $word) {
                if (!in_array($word, $stopwords) && isset($wordFrequency[$word])) {
                    $scores[$i] += $wordFrequency[$word];
                    $relevantWords++;
                }
            }
            
            // Normalize by sentence length and apply position weight
            $scores[$i] = ($relevantWords > 0 ? $scores[$i] / $relevantWords : 0) * $positionWeight[$i];
            
            // Bonus for sentences with numbers (often important statistics/facts)
            if (preg_match('/\d+/', $sentence)) {
                $scores[$i] *= 1.2;
            }
        }
        
        // Get top scoring sentences
        arsort($scores);
        $topSentences = array_slice($scores, 0, $sentenceCount, true);
        ksort($topSentences); // Restore original order
        
        // Build summary
        $summary = '';
        foreach (array_keys($topSentences) as $index) {
            $summary .= $sentences[$index] . ' ';
        }
        
        return trim($summary);
    }
    
    // Get summary
    $summary = improvedSummarize($text, 3);
    
    echo json_encode([
        "success" => true,
        "summary" => $summary
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>