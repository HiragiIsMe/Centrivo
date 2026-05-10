<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('report_code')->unique()->nullable()->after('id');
            $table->text('ban_reason')->nullable()->after('description');
            $table->text('admin_notes')->nullable()->after('ban_reason');
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions')->nullOnDelete()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['related_transaction_id']);
            $table->dropColumn(['report_code', 'ban_reason', 'admin_notes', 'related_transaction_id']);
        });
    }
};
