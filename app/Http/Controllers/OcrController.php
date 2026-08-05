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
        try {
            $request->validate([
                'passport_image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            ]);

            $file = $request->file('passport_image');
            
            // Get prompt from settings or use default
            $defaultPrompt = "استخرج التفاصيل التالية من صورة جواز السفر: الاسم الأول (First Name)، اسم العائلة (Last Name)، تاريخ الميلاد (dob) بصيغة YYYY-MM-DD، رقم الجواز (passport_no)، الجنسية كرمز من حرفين (nationality)، دولة الإصدار كرمز من حرفين (passport_issue_country)، وتاريخ الانتهاء (passport_expiry_date) بصيغة YYYY-MM-DD، والجنس (gender) كحرف واحد M للذكر أو F للأنثى. قم بإرجاع كائن JSON صحيح فقط باللغة الإنجليزية يحتوي على المفاتيح التالية: first_name, last_name, dob, passport_no, nationality, passport_issue_country, passport_expiry_date, gender. لا تضف أي تنسيق markdown مثل ```json";
            
            $prompt = Setting::get('ai_passport_prompt', $defaultPrompt);

            // Call the service which expects an UploadedFile
            $result = $this->geminiService->extractDataFromImage($file, $prompt);

            if ($result) {
                // The service returns the decoded JSON array directly
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Failed to extract data.')
            ], 422);

        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('An error occurred during scanning: ') . $e->getMessage()
            ], 500);
        }
    }
}
