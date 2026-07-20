<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_out')->nullable();
            $table->string('status')->default('present'); // present | late | absent | excused
            $table->string('source')->default('fingerprint'); // fingerprint | manual
            $table->string('note')->nullable(); // e.g. "scanner offline", "excused - doctor's note"
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();

            // Dedupe + reliability: ignore double-taps within a short window,
            // and only queue one guardian email per event (arrival/dismissal/absence).
            $table->dateTime('last_scan_at')->nullable();
            $table->dateTime('guardian_notified_in_at')->nullable();
            $table->dateTime('guardian_notified_out_at')->nullable();
            $table->text('guardian_notify_error')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
