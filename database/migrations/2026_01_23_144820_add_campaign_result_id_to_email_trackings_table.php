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
        Schema::table('email_trackings', function (Blueprint $table) {
            $table->foreignId('campaign_result_id')->nullable()->after('email_recipient_id')->constrained('campaign_results')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_trackings', function (Blueprint $table) {
            $table->dropForeign(['campaign_result_id']);
            $table->dropColumn('campaign_result_id');
        });
    }
};
