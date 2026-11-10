<?php
/**
 * CareerAI - AI Integration (Google Gemini) & Job APIs
 */

// Allow PHP to run for up to 5 minutes without crashing during long AI requests
set_time_limit(300);

require_once __DIR__ . '/env.php';

/**
 * Send CV text to the Gemini API for analysis.
 */
function analyze_cv_with_ai($cvText)
{
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return 'AI API key is missing. Please add GEMINI_API_KEY to your .env file.';

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent';

    $prompt = "You are an expert ATS (Applicant Tracking System) and career coach. 
Analyze the following CV text and provide a professional, structured response. 
Include:
1. Overall Score (out of 100)
2. Top 3 Strengths
3. Top 3 Areas for Improvement
4. Missing Skills
5. Suggested Job Roles

Keep the response concise and format it beautifully using HTML (e.g., <h3>, <ul>, <li>, <strong>). Do not include markdown code blocks like ```html, just return the raw HTML.

Here is the CV text:
" . $cvText;

    $data = ["contents" => [ ["parts" => [["text" => $prompt]]] ]];

    $maxRetries = 3;
    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . $apiKey]);
        
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); 

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            if ($attempt < $maxRetries - 1) { sleep(3); continue; }
            return 'cURL Error: ' . $error;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $code = $result['error']['code'] ?? 0;
            if (($code == 429 || $code == 503 || $code == 500) && $attempt < $maxRetries - 1) {
                sleep(5); continue;
            }
            return 'AI API Error: ' . $result['error']['message'];
        }

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }
        
        return 'Failed to parse AI response.';
    }
    return 'The AI is currently busy. Please try again in a minute.';
}

/**
 * Match a user profile to a job description using AI.
 */
function match_job_with_ai($profileText, $jobTitle, $jobRequirements)
{
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return ['error' => 'AI API key is missing.'];

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent';
    $cleanRequirements = preg_replace('/[^\x20-\x7E]/', '', $jobRequirements);
    
    $prompt = "You are an expert technical recruiter. 
I will give you a candidate's profile and a job description. 
You must return ONLY a JSON object with exactly two keys:
1. 'score': An integer from 0 to 100 representing how well the candidate matches the job.
2. 'feedback': A short HTML paragraph explaining why they match and what skills they are missing.

Candidate Profile:
" . $profileText . "

Job Title: " . $jobTitle . "
Job Requirements: " . $cleanRequirements . "

Return ONLY valid JSON. Do not wrap it in markdown code blocks.";

    $data = [
        "contents" => [ ["parts" => [["text" => $prompt]]] ],
        "generationConfig" => ["responseMimeType" => "application/json"]
    ];

    $maxRetries = 3;
    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . $apiKey]);
        
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            if ($attempt < $maxRetries - 1) { sleep(3); continue; }
            return ['error' => 'cURL Error: ' . $error];
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $code = $result['error']['code'] ?? 0;
            if (($code == 429 || $code == 503 || $code == 500) && $attempt < $maxRetries - 1) {
                sleep(5); continue;
            }
            return ['error' => 'API Error: ' . $result['error']['message']];
        }

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $aiText = $result['candidates'][0]['content']['parts'][0]['text'];
            $aiText = str_replace(['```json', '```'], '', $aiText);
            $aiText = trim($aiText);
            
            $parsed = json_decode($aiText, true);
            if ($parsed !== null && isset($parsed['score'])) {
                return $parsed;
            }
            return ['error' => 'AI returned invalid format.'];
        }
    }
    return ['error' => 'The AI is currently busy. Please try again in a minute.'];
}

/**
 * Fetch jobs from Himalayas API (South Africa)
 */
function fetch_himalayas_jobs($jobTitle)
{
    $query = urlencode($jobTitle);
    $url = "https://himalayas.app/jobs/api/search?q=" . $query . "&country=ZA";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['jobs'] ?? [];
}

/**
 * Generate a learning roadmap using AI.
 */
function generate_roadmap_with_ai($profileText, $careerGoal)
{
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return 'AI API key is missing.';

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent';
    
    $prompt = "You are an expert career counselor. 
Based on the candidate's profile and target career goal, create a 4-stage learning roadmap. 
Keep the response concise and formatted beautifully using HTML. 
Use <h3> for 'Stage 1', 'Stage 2', etc. Use <ul> and <li> for specific skills, technologies, and projects. Do not include markdown code blocks like ```html, just return the raw HTML.

Candidate Profile:
" . $profileText . "

Target Career Goal: " . $careerGoal;

    $data = ["contents" => [ ["parts" => [["text" => $prompt]]] ]];

    $maxRetries = 3;
    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . $apiKey]);
        
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            if ($attempt < $maxRetries - 1) { sleep(3); continue; }
            return 'cURL Error: ' . $error;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $code = $result['error']['code'] ?? 0;
            if (($code == 429 || $code == 503 || $code == 500) && $attempt < $maxRetries - 1) {
                sleep(5); continue;
            }
            return 'AI API Error: ' . $result['error']['message'];
        }

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }
    }
    return 'The AI is currently busy. Please try again in a minute.';
}

/**
 * Chat with the AI Career Assistant.
 */
function chat_with_ai($chatHistory, $profileText)
{
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return 'AI API key is missing.';

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent';
    
    $systemInstruction = "You are CareerAI, a professional career assistant. 
Be concise, helpful, and professional. Do not use emojis.
Here is the user's profile information for context:
" . $profileText;

    $contents = [
        ["role" => "user", "parts" => [["text" => $systemInstruction]]],
        ["role" => "model", "parts" => [["text" => "Understood. I am ready to assist with your career questions."]]]
    ];
    
    foreach ($chatHistory as $msg) {
        $contents[] = [
            "role" => $msg['role'] === 'user' ? 'user' : 'model',
            "parts" => [["text" => $msg['message']]]
        ];
    }

    $data = ["contents" => $contents];

    $maxRetries = 3;
    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . $apiKey]);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            if ($attempt < $maxRetries - 1) { sleep(3); continue; }
            return 'cURL Error: ' . $error;
        }

        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            $code = $result['error']['code'] ?? 0;
            if (($code == 429 || $code == 503 || $code == 500) && $attempt < $maxRetries - 1) {
                sleep(5); continue;
            }
            return 'AI API Error: ' . $result['error']['message'];
        }
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }
    }
    return 'The AI is currently busy. Please try again in a minute.';
}

/**
 * Generate interview questions using AI.
 */
function generate_interview_questions($careerGoal, $profileText)
{
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return 'AI API key is missing.';

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent';
    
    $prompt = "You are an expert technical interviewer. 
The candidate is applying for: " . $careerGoal . ". 
Here is their profile: " . $profileText . "

Generate 5 realistic interview questions for this role. Mix 2 behavioral questions and 3 technical questions based on their skills.
Return ONLY a JSON array of strings. Example: [\"Question 1\", \"Question 2\", \"Question 3\"]. Do not include markdown formatting.";

    $data = [
        "contents" => [ ["parts" => [["text" => $prompt]]] ],
        "generationConfig" => ["responseMimeType" => "application/json"]
    ];

    $maxRetries = 3;
    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . $apiKey]);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            if ($attempt < $maxRetries - 1) { sleep(3); continue; }
            return 'cURL Error: ' . $error;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $code = $result['error']['code'] ?? 0;
            if (($code == 429 || $code == 503 || $code == 500) && $attempt < $maxRetries - 1) { sleep(5); continue; }
            return 'AI API Error: ' . $result['error']['message'];
        }

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $aiText = $result['candidates'][0]['content']['parts'][0]['text'];
            $aiText = str_replace(['```json', '```'], '', $aiText);
            $aiText = trim($aiText);
            $parsed = json_decode($aiText, true);
            if (is_array($parsed)) {
                return $parsed;
            }
            return 'AI returned invalid format.';
        }
    }
    return 'The AI is currently busy. Please try again in a minute.';
}

/**
 * Evaluate an interview answer using AI.
 */
function evaluate_interview_answer($careerGoal, $question, $userAnswer)
{
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return 'AI API key is missing.';

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent';
    
    $prompt = "You are an expert technical interviewer for the role of: " . $careerGoal . ".
Here is the interview question: " . $question . "
Here is the candidate's answer: " . $userAnswer . "

Evaluate the answer. Return ONLY a JSON object with exactly three keys:
1. 'score': An integer from 0 to 100 representing the quality of the answer.
2. 'feedback': A short HTML paragraph explaining what was good and what was missing.
3. 'ideal_answer': A short HTML paragraph showing a better, more professional way to answer.

Return ONLY valid JSON. Do not wrap it in markdown code blocks.";

    $data = [
        "contents" => [ ["parts" => [["text" => $prompt]]] ],
        "generationConfig" => ["responseMimeType" => "application/json"]
    ];

    $maxRetries = 3;
    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . $apiKey]);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            if ($attempt < $maxRetries - 1) { sleep(3); continue; }
            return ['error' => 'cURL Error: ' . $error];
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $code = $result['error']['code'] ?? 0;
            if (($code == 429 || $code == 503 || $code == 500) && $attempt < $maxRetries - 1) { sleep(5); continue; }
            return ['error' => 'API Error: ' . $result['error']['message']];
        }

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $aiText = $result['candidates'][0]['content']['parts'][0]['text'];
            $aiText = str_replace(['```json', '```'], '', $aiText);
            $aiText = trim($aiText);
            $parsed = json_decode($aiText, true);
            if ($parsed !== null && isset($parsed['score'])) {
                return $parsed;
            }
            return ['error' => 'AI returned invalid format.'];
        }
    }
    return ['error' => 'The AI is currently busy. Please try again in a minute.'];
}