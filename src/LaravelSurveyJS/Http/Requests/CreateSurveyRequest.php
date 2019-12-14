<?php

namespace Fruitware\LaravelSurveyJS\LaravelSurveyJS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateSurveyRequest
 *
 * @package Fruitware/LaravelSurveyJS
 */
class CreateSurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  =>  'required|max:255',
        ];
    }
}
