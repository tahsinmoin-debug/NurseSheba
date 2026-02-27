<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nurse_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialization')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('district')->nullable();
            $table->string('thana')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('availability')->default(true);
            $table->string('documents')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurse_profiles');
    }
};
