<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('steam_id', 17)->nullable()->after('email');
            $table->string('steam_profile_url')->nullable()->after('steam_id');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->string('steam_id', 17)->nullable()->after('phone');
            $table->string('steam_profile_url')->nullable()->after('steam_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['address', 'postal_code', 'city']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'postal_code']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address')->default('');
            $table->string('postal_code')->default('');
            $table->string('city')->default('');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['steam_id', 'steam_profile_url']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['steam_id', 'steam_profile_url']);
        });
    }
};
