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
        Schema::table('users', function (Blueprint $table) {
            $table->string('github_url')->nullable()->after('tebex_url');
            $table->string('discord_invite_url')->nullable()->after('github_url');
            $table->string('youtube_url')->nullable()->after('discord_invite_url');
            $table->string('website_url')->nullable()->after('youtube_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['github_url', 'discord_invite_url', 'youtube_url', 'website_url']);
        });
    }
};
