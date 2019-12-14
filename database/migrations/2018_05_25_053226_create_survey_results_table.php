<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSurveyResultsTable
 */
class CreateSurveyResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('survey-manager.database.tables.survey_results'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('survey_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('json');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('survey-manager.database.tables.survey_results'));
    }
}
