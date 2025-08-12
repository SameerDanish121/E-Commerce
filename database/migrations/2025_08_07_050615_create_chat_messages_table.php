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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('customer_id')->index('idx_customer_id');
            $table->enum('sender_type', ['customer', 'admin']);
            $table->integer('sender_id')->nullable();
            $table->longText('message')->nullable();
            $table->enum('media_type', ['image', 'video', 'link', 'file', 'none'])->nullable()->default('none');
            $table->text('media_url')->nullable();
            $table->boolean('is_opened')->nullable()->default(false);
            $table->boolean('is_deleted')->nullable()->default(false);
            $table->bigInteger('reply_to')->nullable()->index('idx_reply_to');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
