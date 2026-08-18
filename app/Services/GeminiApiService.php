<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiApiService
{
    /**
     * Call Gemini API with an image and a prompt to extract JSON data.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $prompt
     * @return array|null The extracted JSON array or null on failure.
     */
    public function extractDataFromImage($image, string $prompt): ?array
    {
        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        
        if (empty($apiKey)) {
            Log::error('Gemini API key is not set.');
            throw new Exception('Gemini API key is missing.');
        }

        // Available models to try in order of preference
        $models = [
            'gemini-2.5-flash',
            'gemini-1.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-pro'
        ];

        $fullPrompt = $prompt . "\n\n" .
            "CRITICAL INSTRUCTIONS:\n" .
            "1. You MUST return ONLY a valid JSON object. Do NOT include markdown tags like ```json or any text outside the JSON.\n" .
            "2. If you cannot read the passport data, return a JSON with key 'error': {\"error\": \"Image is too blurry, dark, or not a passport data page.\"}\n" .
            "3. Read ALL digits and characters with extreme precision.\n" .
            "4. Return exact values without modification.";

        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType() ?: 'image/jpeg';

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $fullPrompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Image
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
            ]
        ];

        foreach ($models as $model) {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey;

            try {
                $response = Http::timeout(25)->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        $responseText = $data['candidates'][0]['content']['parts'][0]['text'];
                        
                        // Clean up potential markdown formatting
                        $responseText = preg_replace('/```json\s*|```/i', '', $responseText);
                        $responseText = trim($responseText);
                        
                        $jsonResult = json_decode($responseText, true);
                        
                        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonResult)) {
                            return $jsonResult;
                        } else {
                            // Try extracting JSON using regex if extra text exists
                            if (preg_match('/\{[\s\S]*\}/', $responseText, $matches)) {
                                $jsonMatch = json_decode($matches[0], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonMatch)) {
                                    return $jsonMatch;
                                }
                            }
                            Log::error('Gemini API returned unparsable JSON', ['response' => $responseText, 'model' => $model]);
                        }
                    }
                } else {
                    Log::warning("Gemini API model {$model} failed: status {$response->status()}", [
                        'body' => $response->body()
                    ]);
                    // If model not found (404), continue to next model in loop
                }
            } catch (Exception $e) {
                Log::warning("Gemini API exception on model {$model}: " . $e->getMessage());
            }
        }

        return null;
    }
}
