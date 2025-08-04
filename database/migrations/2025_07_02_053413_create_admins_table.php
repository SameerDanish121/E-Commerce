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
        Schema::create('admins', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->unique('user_id');
            $table->string('name', 100);
            $table->string('designation', 100)->nullable();
            $table->string('contact_no', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_pic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
