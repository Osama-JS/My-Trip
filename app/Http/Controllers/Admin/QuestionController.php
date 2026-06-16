<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('admin.questions.index');
    }

    public function getData(Request $request)
    {
        $questions = Question::latest()->get(); 

        return response()->json([
            'data' => $questions->map(function ($question) {
                return [
                    'question' => '
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 3px; height: 38px; background: #041741; border-radius: 4px;"></div>
                            <div>
                                <h6 class="mb-1 text-dark fw-bold" style="font-size: 14px; letter-spacing: 0.3px;">' . \Illuminate\Support\Str::limit($question->question_ar, 60) . '</h6>
                                <span class="text-muted d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-language me-1 text-primary" style="opacity: 0.7;"></i> ' . \Illuminate\Support\Str::limit($question->question_en, 60) . '</span>
                            </div>
                        </div>',
                    
                    'answer' => '
                        <div class="d-flex flex-column py-1">
                            <span class="text-dark mb-1" style="font-size: 13px; line-height: 1.4;">' . \Illuminate\Support\Str::limit($question->answer_ar, 65) . '</span>
                            <span class="text-muted" style="font-size: 12px; font-style: italic;">' . \Illuminate\Support\Str::limit($question->answer_en, 65) . '</span>
                        </div>',
                    
                    'status' => $question->active
                        ? '<span class="badge badge-success light border-0 rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 11px; letter-spacing: 0.5px;"><i class="fas fa-check-circle me-1"></i> '.__('Active').'</span>'
                        : '<span class="badge badge-danger light border-0 rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 11px; letter-spacing: 0.5px;"><i class="fas fa-times-circle me-1"></i> '.__('Inactive').'</span>',
                    
                    'actions' => '
                        <div class="d-flex justify-content-end gap-2">
                            <button onclick="editQuestion('.$question->id.')" class="btn btn-sm btn-outline-primary rounded-circle d-flex justify-content-center align-items-center action-btn" style="width: 36px; height: 36px; transition: all 0.2s;" title="'.__('Edit').'">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button onclick="toggleQuestionStatus('.$question->id.')" class="btn btn-sm btn-outline-warning rounded-circle d-flex justify-content-center align-items-center action-btn" style="width: 36px; height: 36px; transition: all 0.2s;" title="'.__('Toggle Status').'">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                            <button onclick="deleteQuestion('.$question->id.')" class="btn btn-sm btn-outline-danger rounded-circle d-flex justify-content-center align-items-center action-btn" style="width: 36px; height: 36px; transition: all 0.2s;" title="'.__('Delete').'">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>'
                ];
            })
        ]);
    }


    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'active' => $request->boolean('active'), 
        ]); 

        $validator = Validator::make($request->all(), [
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar'   => 'required|string',
            'answer_en'   => 'required|string',
            'active'      => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['question_ar', 'question_en', 'answer_ar', 'answer_en']);
        $data['active'] = $request->boolean('active', true);


        $question = Question::create($data);


        return response()->json([
            'success' => true,
            'message' => __('Question saved successfully!'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
         return response()->json([
            'success' => true,
            'question' => $question,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $request->merge([
             'active' => $request->boolean('active'),
         ]);
         
        $validator = Validator::make($request->all(), [
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar'   => 'required|string',
            'answer_en'   => 'required|string',
            'active'      => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['question_ar', 'question_en', 'answer_ar', 'answer_en']);
        $data['active'] = $request->boolean('active', true);

        $question->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Question updated successfully!'),
        ]);
    }

    public function toggleStatus(Question $question)
    {
        $question->update(['active' => !$question->active]);

        return response()->json([
            'success' => true,
            'message' => $question->active ? __('Question activated') : __('Question deactivated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
       $question->delete();

        return response()->json([
            'success' => true,
            'message' => __('Question deleted forever.'),
        ]);
    }
}
