<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Assigned user
            $table->string('ticket_number')->unique(); // Unique ticket identifier
            $table->decimal('price_paid', 10, 2);
            $table->integer('quantity')->default(1);
            $table->string('status')->default('active'); // active, used, cancelled, refunded
            $table->timestamp('purchased_at');
            $table->timestamp('used_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('event_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('attendee'); // attendee, organizer, staff
            $table->boolean('can_view_tickets')->default(false);
            $table->timestamp('assigned_at');
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_users');
        Schema::dropIfExists('tickets');
    }
}; 