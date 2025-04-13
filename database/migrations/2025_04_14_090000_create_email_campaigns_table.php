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
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->unsignedBigInteger('template_id');
            $table->enum('status', ['draft', 'scheduled', 'sent', 'paused', 'canceled'])->default('draft');
            $table->integer('sent_count')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('bounce_count')->default(0);
            $table->json('settings')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('email_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
