<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->enum('role', ['lab','doctor','patient']);
            $table->string('phone', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('license_number', 50)->nullable()->unique();
            $table->string('profile_pic')->nullable();
            $table->string('specialty', 100)->nullable();
            $table->string('wallet_address', 42)->nullable()->unique();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
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
        Schema::dropIfExists('users');
    }
}
