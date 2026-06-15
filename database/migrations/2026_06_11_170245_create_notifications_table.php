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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained();
            $table->enum('channel', ['sms', 'email']);
            $table->enum('priority', ['transactional', 'normal', 'marketing']);
            $table->enum('status', ['queued', 'sent', 'delivered', 'failed'])->index('status_index');
            $table->text('message');
            $table->string('idempotency_key')->nullable()->unique();
            $table->string('provider_ref')->nullable();
            $table->text('error_message')->nullable();
            $table->smallInteger('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index(['subscriber_id', 'created_at'], 'subscriber_id_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
