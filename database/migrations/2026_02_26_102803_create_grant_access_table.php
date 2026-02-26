<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrantAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grant_access', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('authorized_id'); // doctor or lab user id
            $table->enum('role_type', ['doctor', 'lab']); // specify role
            $table->timestamp('granted_at')->nullable();
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamps();

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('authorized_id')->references('id')->on('users')->onDelete('cascade');

            // Prevent duplicates
            $table->unique(['patient_id', 'authorized_id', 'role_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor_patient_access');
    }
}
