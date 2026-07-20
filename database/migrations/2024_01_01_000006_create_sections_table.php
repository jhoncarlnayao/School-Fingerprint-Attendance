<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('grade_level'); // "7" .. "12"
            $table->string('name');        // e.g. "Rizal"
            $table->unsignedInteger('capacity')->nullable(); // max students, null = unlimited
            $table->timestamps();

            $table->unique(['grade_level', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
