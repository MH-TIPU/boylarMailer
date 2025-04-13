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
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->decimal('open_rate', 5, 2)->default(0)->after('bounce_count');
            $table->decimal('click_rate', 5, 2)->default(0)->after('open_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn(['open_rate', 'click_rate']);
        });
    }
};
