<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billboards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('gradient_from')->default('#628ECB');
            $table->string('gradient_to')->default('#8AAEE0');
            $table->string('image_path')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('billboards')->insert([
            ['title' => 'Diskon Servis AC 20%', 'subtitle' => 'Khusus area Sumbersari dan sekitarnya.', 'gradient_from' => '#628ECB', 'gradient_to' => '#8AAEE0', 'image_path' => null, 'order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Buka Puasa Lebih Praktis', 'subtitle' => 'Cari katering harian terbaik untuk Ramadan.', 'gradient_from' => '#6366f1', 'gradient_to' => '#a855f7', 'image_path' => null, 'order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Ingin Jadi Mitra?', 'subtitle' => 'Daftar sekarang dan mulai tawarkan jasamu.', 'gradient_from' => '#1e293b', 'gradient_to' => '#0f172a', 'image_path' => null, 'order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('billboards');
    }
};
