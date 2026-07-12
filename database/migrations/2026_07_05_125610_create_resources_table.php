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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->enum('category', ['scripts', 'mlos', 'eup', 'vehicles']);
            $table->enum('framework', ['esx', 'qb-core', 'standalone', 'ox']);
            $table->decimal('price', 8, 2)->default(0);
            $table->string('download_url')->nullable();
            $table->string('tebex_url')->nullable();
            $table->string('thumbnail_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->index(['category', 'framework']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
