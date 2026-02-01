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
        Schema::table('users', function (Blueprint $table) {
            $table->string('bio')->nullable()->after('email');
            $table->string('profile_picture')->nullable()->after('bio');
            $table->json('speaking_goals')->nullable()->after('profile_picture');
            $table->json('notification_preferences')->nullable()->after('speaking_goals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'profile_picture', 'speaking_goals', 'notification_preferences']);
        });
    }
};
