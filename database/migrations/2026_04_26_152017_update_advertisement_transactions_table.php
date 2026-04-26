<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisement_transactions', function (Blueprint $table) {
            $table->foreignId('ad_package_id')->nullable()->after('advertisement_id')->constrained('ad_packages')->nullOnDelete();
            $table->string('snap_token')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('advertisement_transactions', function (Blueprint $table) {
            $table->dropForeign(['ad_package_id']);
            $table->dropColumn(['ad_package_id', 'snap_token']);
        });
    }
};
