<?php
session_start();
header('Content-Type: application/json');

// Get the text from POST request
$text = isset($_POST['text']) ? $_POST['text'] : '';

if (empty($text)) {
    echo json_encode([
        'success' => false,
        'message' => 'No text provided'
    ]);
    exit;
}

// Try to use Hugging Face API if available
$useHuggingFace = true;

if ($useHuggingFace) {
    // Hugging Face API endpoint
    $url = 'https://api-inference.huggingface.co/models/facebook/bart-large-cnn';

    // Make API request to Hugging Face
    $ch = curl_init($url);

    // Prepare data for quiz generation
    $data = [
        'inputs' => 'Create a quiz with 5 multiple-choice questions based on the following text. Focus only on important and relevant information from the text. Each question should have 4 options (A, B, C, D) with one correct answer marked. Format it as a structured quiz with question numbers and correct answers at the end: ' . $text,
        'parameters' => [
            'max_length' => 1000,
            'temperature' => 0.7
        ]
    ];

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        // Add your API key if you have one
        // 'Authorization: Bearer hf_your_api_key'
    ]);

    // Execute cURL request
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if (!$err) {
        $result = json_decode($response, true);

        if (isset($result[0]['generated_text'])) {
            $quiz = $result[0]['generated_text'];
            
            // Format the quiz properly
            $quiz = formatQuiz($quiz);
            
            // Store quiz in session
            $_SESSION['quiz'] = $quiz;
            
            echo json_encode([
                'success' => true,
                'quiz' => $quiz
            ]);
            exit;
        }
    }
}

// Fallback method if Hugging Face API fails
$quiz = generateSmartQuiz($text);

echo json_encode([
    'success' => true,
    'quiz' => $quiz,
    'note' => 'Generated using fallback method'
]);

// Function to format the quiz properly
function formatQuiz($quiz) {
    // Ensure quiz has proper markdown format
    $quiz = "## Quiz\n\n" . $quiz;
    
    // Ensure each question has a header
    $quiz = preg_replace('/Question (\d+):/i', "### Question $1:", $quiz);
    
    // Ensure options are properly formatted
    $quiz = preg_replace('/^([A-D])\.\s/m', "$1. ", $quiz);
    
    return $quiz;
}

// Improved fallback quiz generation function
function generateSmartQuiz($text) {
    // Extract important sentences
    $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    
    // Remove very short sentences
    $sentences = array_filter($sentences, function($sentence) {
        return str_word_count($sentence) > 6;
    });
    
    if (count($sentences) < 5) {
        // Not enough substantial sentences, extract main topics
        $words = str_word_count($text, 1);
        // Remove stop words
        $stopWords = ['the', 'and', 'that', 'this', 'with', 'for', 'from', 'have', 'has', 'had', 'was', 'were'];
        $words = array_diff($words, $stopWords);
        $wordCounts = array_count_values($words);
        arsort($wordCounts); // Sort by frequency
        
        $topics = array_slice(array_keys($wordCounts), 0, 5);
        
        $quiz = "## Quiz based on the provided text\n\n";
        
        for ($i = 0; $i < min(5, count($topics)); $i++) {
            $topic = $topics[$i];
            $quiz .= "### Question " . ($i + 1) . ":\n";
            $quiz .= "What is the significance of \"" . $topic . "\" in the context of the text?\n\n";
            $quiz .= "A. Most relevant interpretation\n";
            $quiz .= "B. Secondary interpretation\n";
            $quiz .= "C. Tertiary interpretation\n";
            $quiz .= "D. Irrelevant interpretation\n\n";
            $quiz .= "Correct Answer: A\n\n";
        }
        
        return $quiz;
    }
    
    // Select 5 most substantial sentences for quiz questions
    $sentenceScores = [];
    foreach ($sentences as $index => $sentence) {
        // Score based on length and presence of key terms
        $score = str_word_count($sentence) * 0.5;
        
        // Check for factual content words
        $factWords = ['is', 'are', 'was', 'were', 'has', 'have', 'can', 'will', 'should'];
        foreach ($factWords as $word) {
            if (preg_match('/\b' . $word . '\b/i', $sentence)) {
                $score += 2;
            }
        }
        
        // Higher score for sentences with numbers or dates
        if (preg_match('/\d+/', $sentence)) {
            $score += 3;
        }
        
        $sentenceScores[$index] = $score;
    }
    
    arsort($sentenceScores);
    $topSentenceIndices = array_keys(array_slice($sentenceScores, 0, 5, true));
    
    $quiz = "## Quiz based on the provided text\n\n";
    $questionCount = 0;
    
    foreach ($topSentenceIndices as $index) {
        $sentence = $sentences[$index];
        $questionCount++;
        
        // Determine what type of question to create
        if (preg_match('/\b(is|are|was|were)\b/i', $sentence)) {
            // Create a true/false question with context
            $quiz .= "### Question " . $questionCount . ":\n";
            $quiz .= "Based on the text, is the following statement true? \"" . $sentence . "\"\n\n";
            $quiz .= "A. True\n";
            $quiz .= "B. False\n";
            $quiz .= "C. Partially true\n";
            $quiz .= "D. Not mentioned in the text\n\n";
            $quiz .= "Correct Answer: A\n\n";
        } else {
            // Create a fill-in-the-blank question
            $words = explode(' ', $sentence);
            
            if (count($words) > 5) {
                // Find a significant word to blank out
                $keywordPosition = null;
                $keywords = [];
                
                // Extract potential keywords (nouns, verbs, adjectives)
                foreach ($words as $pos => $word) {
                    if (strlen($word) > 4 && 
                        !in_array(strtolower($word), ['about', 'after', 'again', 'below', 'could', 'every', 'first', 'found', 'great', 'house', 'large', 'learn', 'never', 'other', 'place', 'small', 'study', 'their', 'there', 'these', 'think', 'three', 'water', 'where', 'which', 'world', 'would', 'write'])) {
                        $keywords[$pos] = $word;
                    }
                }
                
                if (!empty($keywords)) {
                    $keywordPosition = array_rand($keywords);
                    $keywordToRemove = $keywords[$keywordPosition];
                    
                    // Create options including the correct answer
                    $options = [$keywordToRemove];
                    
                    // Add false options
                    $allWords = str_word_count($text, 1);
                    $allWords = array_unique($allWords);
                    $filteredWords = array_filter($allWords, function($word) use ($keywordToRemove) {
                        return strlen($word) > 3 && $word !== $keywordToRemove;
                    });
                    
                    // Shuffle and take 3 words
                    shuffle($filteredWords);
                    $falseOptions = array_slice($filteredWords, 0, 3);
                    
                    // Combine all options and shuffle
                    $allOptions = array_merge([$keywordToRemove], $falseOptions);
                    shuffle($allOptions);
                    
                    // Find the position of the correct answer
                    $correctOptionIndex = array_search($keywordToRemove, $allOptions);
                    $correctOptionLetter = chr(65 + $correctOptionIndex); // A, B, C, or D
                    
                    // Create the blanked sentence
                    $wordsCopy = $words;
                    $wordsCopy[$keywordPosition] = "________";
                    $blankedSentence = implode(' ', $wordsCopy);
                    
                    $quiz .= "### Question " . $questionCount . ":\n";
                    $quiz .= "Complete the following sentence from the text: \"" . $blankedSentence . "\"\n\n";
                    
                    for ($i = 0; $i < count($allOptions); $i++) {
                        $quiz .= chr(65 + $i) . ". " . $allOptions[$i] . "\n";
                    }
                    
                    $quiz .= "\nCorrect Answer: " . $correctOptionLetter . "\n\n";
                } else {
                    // Fallback: main idea question
                    $quiz .= "### Question " . $questionCount . ":\n";
                    $quiz .= "Which of the following best describes the main idea of this sentence: \"" . $sentence . "\"\n\n";
                    $quiz .= "A. The most accurate description\n";
                    $quiz .= "B. A partially accurate description\n";
                    $quiz .= "C. An inaccurate description\n";
                    $quiz .= "D. A completely unrelated description\n\n";
                    $quiz .= "Correct Answer: A\n\n";
                }
            }
        }
    }
    
    return $quiz;
}
?>