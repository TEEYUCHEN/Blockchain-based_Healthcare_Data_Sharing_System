<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('test_type')->nullable();
            $table->text('result')->nullable();
            $table->string('report_file')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_report');
    }
}
