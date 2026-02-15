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
                   
                    'question' => '<b>AR:</b> ' . \Illuminate\Support\Str::limit($question->question_ar, 50) . 
                                '<br><b>EN:</b> ' . \Illuminate\Support\Str::limit($question->question_en, 50),
                    
                    'answer' => '<b>AR:</b> ' . \Illuminate\Support\Str::limit($question->question_ar, 50) . 
                                '<br><b>EN:</b> ' . \Illuminate\Support\Str::limit($question->question_en, 50),
                    
                    
                    'status' => $question->active
                        ? '<span class="badge bg-success">'.__('Active').'</span>'
                        : '<span class="badge bg-danger">'.__('Inactive').'</span>',
                    
                   
                    'actions' => '
                        <div class="d-flex">
                            <button onclick="editQuestion('.$question->id.')" class="btn btn-primary btn-sm me-1" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button onclick="toggleQuestionStatus('.$question->id.')" class="btn btn-warning btn-sm me-1" title="Toggle Status">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                            <button onclick="deleteQuestion('.$question->id.')" class="btn btn-danger btn-sm" title="Delete">
                                <i class="fa fa-trash"></i>
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
