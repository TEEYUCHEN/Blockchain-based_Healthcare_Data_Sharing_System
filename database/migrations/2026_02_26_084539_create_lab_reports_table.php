<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('patient_id');

            $table->string('test_type');
            $table->text('result');

            $table->string('report_file'); // S3 path

            // ✅ NEW (important)
            $table->string('original_filename')->nullable();
            $table->string('file_hash', 64)->nullable();

            $table->timestamps();

            // Optional (recommended)
            $table->foreign('lab_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_reports');
    }
}
