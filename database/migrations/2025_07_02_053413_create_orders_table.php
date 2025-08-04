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
        Schema::create('orders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('customer_id')->nullable()->index('customer_id');
            $table->dateTime('order_datetime')->nullable()->useCurrent();
            $table->enum('status', ['Pending', 'Dispatched', 'Canceled', 'Completed'])->nullable()->default('Pending');
            $table->double('total_payment');
            $table->boolean('delivered_on_time')->nullable();
            $table->text('delivery_address');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
