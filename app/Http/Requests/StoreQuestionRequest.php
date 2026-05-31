<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'reading_text_id' => 'nullable|exists:reading_texts,id',
            'question_group_id' => 'nullable|exists:question_groups,id',
            'content' => 'required',
            'type' => 'required|in:multiple_choice,multiple_choice_complex,boolean_grid,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'options_single' => 'required_if:type,multiple_choice|array',
            'options_complex' => 'required_if:type,multiple_choice_complex|array',
            'options_grid' => 'required_if:type,boolean_grid|array',
            'tags' => 'nullable|string',
        ];
    }
}
