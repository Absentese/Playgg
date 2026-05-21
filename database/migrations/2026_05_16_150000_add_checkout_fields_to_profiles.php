<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('first_name', 50)->nullable()->after('user_id');
            $table->string('last_name', 50)->nullable()->after('first_name');
            $table->string('contact_email')->nullable()->after('last_name');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'contact_email']);
        });
    }
};
