<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_recipients', function (Blueprint $table) {
            // Drop tracking related columns as they are moved to campaign_results
            $table->dropForeign(['campaign_id']); // Drop the foreign key constraint first
            $table->dropColumn([
                'campaign_id',
                'tracking_token',
                'status',
                'sent_at',
                'opened_at',
                'ip_address',
                'user_agent'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_recipients', function (Blueprint $table) {
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('tracking_token')->unique()->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'opened'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
        });
    }
};
