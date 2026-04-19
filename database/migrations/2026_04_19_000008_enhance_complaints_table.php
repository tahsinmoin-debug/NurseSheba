<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('complaint_type')->default('Service Quality Issue')->after('nurse_id');
            $table->foreignId('booking_id')->nullable()->after('complaint_type')->constrained()->onDelete('set null');
            $table->string('reporter_role')->default('patient')->after('status');
            $table->text('admin_reply')->nullable()->after('reporter_role');
            $table->timestamp('replied_at')->nullable()->after('admin_reply');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn(['complaint_type', 'booking_id', 'reporter_role', 'admin_reply', 'replied_at']);
        });
    }
};
