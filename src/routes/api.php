<?php


Route::group(
    [
        'namespace'     =>  'App\Http\Controllers\API',
        'middleware'    =>  config('survey-manager.api_middleware'),
        'prefix'        =>  config('survey-manager.api_prefix'),
    ],
    function () {
        Route::resource('/survey', 'SurveyAPIController', ['only' => [
            'index', 'store', 'update', 'destroy', 'show',
        ]]);
        Route::resource('/survey/{survey}/result', 'SurveyResultAPIController');
        Route::post('/survey/image/upload', 'SurveyAPIController@upload');
        Route::get('/survey/patients/all', 'SurveyAPIController@getPatients');
        Route::get('/survey/patient/{id}', 'SurveyAPIController@getPatient');
    }
);
