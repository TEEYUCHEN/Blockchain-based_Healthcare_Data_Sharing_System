<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->enum('uploaded_by_role', ['patient', 'doctor', 'lab']);
            $table->string('category')->nullable(); // optional: "xray", "report", etc.

            $table->string('original_filename');
            $table->string('stored_path');          // storage path
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('file_hash')->nullable();
            $table->string('blockchain_tx_hash')->nullable();

            $table->enum('verification_status', ['unverified', 'verified', 'rejected'])->default('unverified');
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

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
        Schema::dropIfExists('medical_records');
    }
}
