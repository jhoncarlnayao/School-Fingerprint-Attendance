<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 30); // created, updated, deleted, emailed, assigned...
            $table->string('subject_type', 60)->nullable(); // Student, Teacher, Section, Announcement...
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description', 500); // human readable log line, e.g. "Admin edited Jane Cruz's assignment"
            $table->boolean('is_warning')->default(false); // true = flagged (e.g. duplicate subject conflict)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
