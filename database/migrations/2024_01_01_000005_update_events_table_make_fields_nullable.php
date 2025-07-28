<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->datetime('start_date')->nullable()->change();
            $table->datetime('end_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->datetime('start_date')->nullable(false)->change();
            $table->datetime('end_date')->nullable(false)->change();
        });
    }
}; 