<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('base_price', 15, 2)->default(0)->after('request_id');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('base_price');
            $table->decimal('admin_fee', 15, 2)->default(0)->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'tax_amount', 'admin_fee']);
        });
    }
};
