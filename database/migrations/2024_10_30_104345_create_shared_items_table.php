<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shared_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to_do_item_id');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('shared_with_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_items');
    }
};
