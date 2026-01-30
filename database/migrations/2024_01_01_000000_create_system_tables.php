<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->integer('age');
            $table->float('reputation_score')->default(0.0);
            $table->integer('version')->default(1); // Optimistic locking
            $table->timestamps();
        });

        // Hobbies Table
        Schema::create('hobbies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Hobby_User Pivot
        Schema::create('hobby_user', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('hobby_id');
            $table->primary(['user_id', 'hobby_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hobby_id')->references('id')->on('hobbies')->onDelete('cascade');
        });

        // Relationships Table (Mutual)
        Schema::create('relationships', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('friend_id');
            $table->boolean('is_blocked')->default(false); // For blocked logic
            $table->timestamp('created_at')->useCurrent();

            // Composite primary key prevents duplicates at DB level
            $table->primary(['user_id', 'friend_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Custom Token Table (Lightweight Auth)
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token_hash')->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_tokens');
        Schema::dropIfExists('relationships');
        Schema::dropIfExists('hobby_user');
        Schema::dropIfExists('hobbies');
        Schema::dropIfExists('users');
    }
};
