<?php


Route::group(
    [
        'namespace'     =>  'Fruitware\LaravelSurveyJS\LaravelSurveyJS\Http\Controllers\API',
        'middleware'    =>  config('survey-manager.api_middleware'),
        'prefix'        =>  config('survey-manager.api_prefix'),
    ],
    function () {
        Route::resource('/survey', 'SurveyAPIController', ['only' => [
            'index', 'store', 'update', 'destroy', 'show',
        ]]);
        Route::resource('/survey/{survey}/result', 'SurveyResultAPIController');
    }
);
