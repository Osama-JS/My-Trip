<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiApiService;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class OcrController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiApiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function scanPassport(Request $request)
    {
        $locale = app()->getLocale();

        // 1. Validate Input File with clean, localized error messages
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'passport_image' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'passport_image.required' => $locale === 'ar' 
                ? 'يرجى إرفاق أو رفع صورة جواز السفر أولاً.' 
                : 'Please upload a passport image first.',
            'passport_image.file'     => $locale === 'ar' 
                ? 'الملف المرفوع غير صالح كملف صورة.' 
                : 'The uploaded file is invalid.',
            'passport_image.image'    => $locale === 'ar' 
                ? 'يجب أن يكون الملف المرفوع صورة صالحة.' 
                : 'The uploaded file must be a valid image.',
            'passport_image.mimes'    => $locale === 'ar' 
                ? 'صيغة الصورة غير مدعومة. الصيغ المقبولة: JPG, PNG, WEBP.' 
                : 'Unsupported format. Allowed formats: JPG, PNG, WEBP.',
            'passport_image.max'      => $locale === 'ar' 
                ? 'حجم الصورة كبير جداً، الحد الأقصى المسموح به هو 10 ميجابايت.' 
                : 'Image size exceeds maximum limit of 10MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('passport_image');
            
            // Comprehensive passport OCR prompt
            $defaultPrompt = "You are a professional passport data extractor. Analyze the provided passport image and extract the following traveler details:
- first_name: Given names / First name (in English uppercase or titlecase)
- last_name: Surname / Family name (in English uppercase or titlecase)
- dob: Date of birth in YYYY-MM-DD format
- passport_no: Passport number (alphanumeric, exactly as printed)
- nationality: 2-letter ISO country code (e.g. SA, US, GB, EG, AE, YE)
- passport_issue_country: 2-letter ISO country code of issue
- passport_expiry_date: Passport expiration date in YYYY-MM-DD format
- gender: 'M' for male, 'F' for female

CRITICAL INSTRUCTIONS:
1. If the image is NOT a passport or the data is completely unreadable/blurry, return a JSON object with an 'error' key explaining why (e.g. {\"error\": \"Image is blurry or not a valid passport page.\"}).
2. Do not hallucinate or guess. If a specific field is unclear, set it to null.
3. Return ONLY a pure JSON object without markdown code blocks.";

            $prompt = Setting::get('ai_passport_prompt', $defaultPrompt);

            $result = $this->geminiService->extractDataFromImage($file, $prompt);

            if ($result) {
                if (!empty($result['error'])) {
                    return response()->json([
                        'success' => false,
                        'message' => $locale === 'ar' 
                            ? 'تعذر قراءة بيانات الجواز بدقة: ' . $result['error'] . ' يرجى رفع صورة أوضح لصفحة الجواز أو إدخال البيانات يدوياً.' 
                            : 'Could not extract passport data: ' . $result['error'] . ' Please upload a clearer photo or enter details manually.',
                        'data'    => $result
                    ], 422);
                }

                // Check if at least some core fields were found
                $hasCoreData = !empty($result['first_name']) || !empty($result['last_name']) || !empty($result['passport_no']);
                if (!$hasCoreData) {
                    return response()->json([
                        'success' => false,
                        'message' => $locale === 'ar' 
                            ? 'لم نتمكن من التعرف على بيانات الجواز من هذه الصورة. يرجى التأكد من رفع صورة واضحة ومستقيمة لصفحة البيانات الرئيسية، أو تعبئة البيانات يدوياً.' 
                            : 'Could not recognize passport details from this image. Please ensure a clear photo of the main info page is uploaded or fill fields manually.'
                    ], 422);
                }

                return response()->json([
                    'success' => true,
                    'message' => $locale === 'ar' ? 'تم استخراج بيانات الجواز بنجاح.' : 'Passport data extracted successfully.',
                    'data'    => $result
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $locale === 'ar' 
                    ? 'تعذر استخراج البيانات من الصورة. يرجى التأكد من وضوح الصورة وإضاءتها، أو تعبئة البيانات يدوياً.' 
                    : 'Failed to extract data. Please ensure the passport page is well-lit and clear, or enter details manually.'
            ], 422);

        } catch (\Exception $e) {
            Log::error('OCR Exception: ' . $e->getMessage());
            
            $isApiKeyMissing = str_contains($e->getMessage(), 'API key');
            $userMsg = $isApiKeyMissing 
                ? ($locale === 'ar' ? 'خدمة الذكاء الاصطناعي غير مفعلة حالياً. يمكنك تعبئة بيانات الجواز يدوياً بسهولة.' : 'AI service is currently not configured. Please fill details manually.')
                : ($locale === 'ar' ? 'حدث خطأ غير متوقع أثناء معالجة الصورة. يرجى إعادة المحاولة أو تعبئة البيانات يدوياً.' : 'An error occurred during AI processing. Please retry or enter details manually.');

            return response()->json([
                'success' => false,
                'message' => $userMsg,
                'debug'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
