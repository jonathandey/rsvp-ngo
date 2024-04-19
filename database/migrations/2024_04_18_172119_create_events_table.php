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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('host_key');
            $table->string('public_key');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_day')->nullable();
            $table->string('start_time')->nullable();
            $table->date('end_day')->nullable();
            $table->string('end_time')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('host_key');
            $table->unique('public_key');

            $table->index('host_key');
            $table->index('public_key');
            $table->index(['host_key', 'public_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
