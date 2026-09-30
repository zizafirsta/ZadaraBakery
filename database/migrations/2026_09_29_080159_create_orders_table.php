<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('promo_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invoice_no', 30)->unique();
            $table->enum('fulfillment_type', ['pickup', 'delivery']);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'ready',
                'shipped',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->dateTime('scheduled_at')->nullable(); // Tanggal/Jam pickup atau delivery
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
