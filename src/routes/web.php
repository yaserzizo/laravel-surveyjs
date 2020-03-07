<?php


Route::group(
    [
        'namespace'     =>  'App\Http\Controllers',
        'middleware'    =>  config('survey-manager.route_middleware'),
        'prefix'        =>  config('survey-manager.route_prefix'),
    ],
    function () {
        Route::get('/{surveySlug}', 'TemplateController@runSurvey')->name('survey-manager.run');
    }
);

Route::group(
    [
        'namespace'     =>  'App\Http\Controllers',
        'prefix'        =>  config('survey-manager.admin_prefix'),
        'middleware'    =>  config('survey-manager.admin_middleware'),
    ],
    function () {
        Route::view('{vue?}', 'survey-manager::admin')->where('vue', '[\/\w\.-]*')->name('survey-manager.admin');
    }
);
