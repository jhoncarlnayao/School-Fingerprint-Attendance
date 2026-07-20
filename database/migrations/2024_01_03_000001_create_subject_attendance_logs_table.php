<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject'); // snapshot of the teacher's assigned subject at time of marking
            $table->string('grade_level');
            $table->string('section');
            $table->date('date');
            $table->string('status')->default('present'); // present | late | absent | excused
            $table->string('note')->nullable();

            // One guardian email per student/subject/day.
            $table->dateTime('guardian_notified_at')->nullable();
            $table->text('guardian_notify_error')->nullable();

            $table->timestamps();

            // A teacher takes attendance once per subject, per student, per day.
            $table->unique(['student_id', 'teacher_id', 'date'], 'subject_attendance_unique');
            $table->index(['date', 'status']);
            $table->index(['teacher_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_attendance_logs');
    }
};
