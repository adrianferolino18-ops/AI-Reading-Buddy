<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('saved_words') && Schema::hasColumn('saved_words', 'user_id')) {
            Schema::table('saved_words', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('saved_words') && Schema::hasColumn('saved_words', 'user_id')) {
            Schema::table('saved_words', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable(false)->change();
            });
        }
    }
};
