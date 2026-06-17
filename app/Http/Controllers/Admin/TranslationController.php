<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    /**
     * Translate text to target language using Google Translate free endpoint.
     */
    public function translate(Request $request)
    {
        $text = $request->input('text');
        $target = $request->input('target', 'en'); // 'en' or 'ar'
        
        if (empty($text)) {
            return response()->json([
                'success' => true,
                'translated' => ''
            ]);
        }
        
        try {
            // Unofficial Google Translate API (free web parsing endpoint)
            $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" . $target . "&dt=t&q=" . urlencode($text);
            
            $response = Http::withoutVerifying()
                ->connectTimeout(5)
                ->timeout(10)
                ->get($url);
            
            if ($response->successful()) {
                $result = $response->json();
                $translatedText = '';
                
                if (isset($result[0]) && is_array($result[0])) {
                    foreach ($result[0] as $sentence) {
                        $translatedText .= $sentence[0] ?? '';
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'translated' => $translatedText
                ]);
            }
            
            Log::error("Google Translate returned status: " . $response->status(), [
                'body' => $response->body()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Translation request exception: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Translation failed'
        ], 500);
    }
}
