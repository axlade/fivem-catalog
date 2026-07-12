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
        Schema::rename('resource_reviews', 'resource_ratings');

        Schema::table('resource_ratings', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_ratings', function (Blueprint $table) {
            $table->text('comment')->nullable();
        });

        Schema::rename('resource_ratings', 'resource_reviews');
    }
};
