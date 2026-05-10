<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->string('nik')->nullable()->after('phone');
            $table->string('ktp_path')->nullable()->after('nik');          
            $table->string('selfie_path')->nullable()->after('ktp_path');  

            $table->string('bank_name')->nullable()->after('selfie_path');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');

            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])
                  ->default('unverified')->after('bank_account_name');
            $table->text('rejection_reason')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'nik', 'ktp_path', 'selfie_path',
                'bank_name', 'bank_account_number', 'bank_account_name',
                'verification_status', 'rejection_reason', 'verified_at',
            ]);
        });
    }
};
