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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('longitude');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('service_type', ['home_service', 'on_site'])->default('home_service')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
    }
};
