<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->unique()->after('email');
            $table->string('role', 20)->default('tenant')->after('phone');
            $table->string('status', 20)->default('active')->after('role');
            $table->string('verification_status', 20)->default('unverified')->after('status');
            $table->string('nid_number', 30)->nullable()->after('verification_status');
            $table->string('address')->nullable()->after('nid_number');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropUnique(['phone']);
            $table->dropColumn(['phone', 'role', 'status', 'verification_status', 'nid_number', 'address']);
        });
    }
};
