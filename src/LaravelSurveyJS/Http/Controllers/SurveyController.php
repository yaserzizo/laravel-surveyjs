<?php

namespace Fruitware\LaravelSurveyJS\LaravelSurveyJS\Http\Controllers;

use Illuminate\Routing\Controller;
use Fruitware\LaravelSurveyJS\LaravelSurveyJS\Models\Survey;

/**
 * Class SurveyController
 *
 * @package Fruitware/LaravelSurveyJS
 */
class SurveyController extends Controller
{
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function runSurvey($slug)
    {
        $survey = Survey::where('slug', $slug)->firstOrFail();

        return view('survey-manager::survey', [
            'survey'    =>  $survey,
        ]);
    }
}
