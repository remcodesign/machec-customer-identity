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
        // usr_addresses is a nested-only concept (Step 1, specs-overview) —
        // it cannot exist without its User, so it cascades away with one.
        // usr_addresses.user_id should cascade on delete to avoid FK violations when a User is deleted.
        Schema::table('usr_addresses', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // usr_audit_log.user_id is already nullable "for system actions"
        // (D63) — a deleted actor's history is preserved, not destroyed,
        // by falling back to that same null-actor state.
        // usr_audit_log.user_id should be set to null on delete to preserve the audit log without the deleted actor.
        Schema::table('usr_audit_log', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usr_addresses', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('usr_audit_log', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
