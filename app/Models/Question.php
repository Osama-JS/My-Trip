<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
  protected $fillable = [
        'question_ar', 
        'question_en', 
        'answer_ar', 
        'answer_en', 
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
