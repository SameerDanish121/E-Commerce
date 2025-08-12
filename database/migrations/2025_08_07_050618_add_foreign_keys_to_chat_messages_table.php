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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreign(['customer_id'], 'chat_messages_customer_fk')->references(['id'])->on('customers')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['reply_to'], 'chat_messages_reply_fk')->references(['id'])->on('chat_messages')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign('chat_messages_customer_fk');
            $table->dropForeign('chat_messages_reply_fk');
        });
    }
};
