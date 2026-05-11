<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_disputed')->default(false)->after('payment_status');
            $table->timestamp('disputed_at')->nullable()->after('is_disputed');
            $table->string('disputed_by')->nullable()->after('disputed_at'); // 'user_ban' | 'seller_ban' | 'service_ban'
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('ban_report_code')->nullable()->after('banned_at');
            $table->text('ban_reason')->nullable()->after('ban_report_code');
        });

        DB::table('settings')->insert([
            ['key' => 'admin_whatsapp', 'value' => '628123456789', 'type' => 'text', 'label' => 'Nomor WhatsApp Admin (format: 628xxx)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['is_disputed', 'disputed_at', 'disputed_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ban_report_code', 'ban_reason']);
        });

        DB::table('settings')->where('key', 'admin_whatsapp')->delete();
    }
};
