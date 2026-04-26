<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, image
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'platform_name', 'value' => 'Centrivo', 'type' => 'text', 'label' => 'Nama Platform', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admin_fee', 'value' => '2500', 'type' => 'number', 'label' => 'Biaya Admin per Transaksi (Rp)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_percentage', 'value' => '11', 'type' => 'number', 'label' => 'Persentase PPN (%)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'min_withdrawal', 'value' => '10000', 'type' => 'number', 'label' => 'Minimum Penarikan (Rp)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
