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
        Schema::create('scheduled_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('email_campaign_id');
            $table->unsignedBigInteger('lead_id');
            $table->enum('status', ['scheduled', 'sent', 'failed'])->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('email_campaign_id')->references('id')->on('email_campaigns')->onDelete('cascade');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_emails');
    }
};