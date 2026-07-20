<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // The template ID/UID your fingerprint SDK (ZKTeco, SecuGen, etc.)
            // assigns to this student when enrolled on the scanner device.
            // We only store the ID here — enrollment itself happens on the
            // scanner's own software, not in BANTAY.
            $table->string('fingerprint_id')->nullable()->unique()->after('student_no');

            // Guardian's email for attendance notifications. Kept separate
            // from guardian_contact, which is a phone number.
            $table->string('guardian_email')->nullable()->after('guardian_contact');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['fingerprint_id', 'guardian_email']);
        });
    }
};
