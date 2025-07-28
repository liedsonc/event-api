<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Event owner
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('max_persons_per_ticket')->default(1); // For group tickets
            $table->integer('available_quantity')->nullable(); // NULL = unlimited
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('event_ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
            $table->decimal('event_price', 10, 2); // Price for this specific event
            $table->integer('quantity_available')->nullable(); // Override for this event
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['event_id', 'ticket_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_ticket_types');
        Schema::dropIfExists('ticket_types');
    }
}; 