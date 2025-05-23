<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik', 16)->unique();
            $table->string('nip', 6)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('place_of_birth', 50);
            $table->date('date_of_birth');
            $table->string('phone', 17);
            $table->string('address', 256);
            $table->smallInteger('leave_allowance')->default(0);
            $table->smallInteger('sick_allowance')->default(0);
            $table->smallInteger('give_birth_allowance')->default(0);
            $table->date('date_of_entry')->nullable();
            $table->date('mutation_date')->nullable();
            $table->string('lod_start', 256)->nullable();
            $table->string('lod_mutation', 256)->nullable();
            $table->string('lod_stop', 256)->nullable();
            $table->string('profile_picture', 256)->nullable();
            $table->string('signature', 256)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
