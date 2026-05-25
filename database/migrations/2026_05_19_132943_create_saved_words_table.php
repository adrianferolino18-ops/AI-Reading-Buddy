<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('saved_words')) {
            Schema::create('saved_words', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('word');
                $table->text('definition')->nullable();
                $table->text('context')->nullable();
                
                // Core columns tracking the progressive hints
                $table->text('hint_1')->nullable(); 
                $table->text('hint_2')->nullable(); 
                $table->text('hint_3')->nullable(); 
                
                $table->timestamps();
            });
        } else {
            Schema::table('saved_words', function (Blueprint $table) {
                if (!Schema::hasColumn('saved_words', 'context')) {
                    $table->text('context')->nullable();
                }
                if (!Schema::hasColumn('saved_words', 'hint_1')) {
                    $table->text('hint_1')->nullable();
                }
                if (!Schema::hasColumn('saved_words', 'hint_2')) {
                    $table->text('hint_2')->nullable();
                }
                if (!Schema::hasColumn('saved_words', 'hint_3')) {
                    $table->text('hint_3')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_words');
    }
};