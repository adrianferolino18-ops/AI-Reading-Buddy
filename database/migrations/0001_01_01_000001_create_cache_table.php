<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saved_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('word');
            $table->text('definition');
            $table->timestamps();
            
            // Avoid duplicate savings of the same word by a user per module
            $table->unique(['user_id', 'module_id', 'word']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_words');
    }
};