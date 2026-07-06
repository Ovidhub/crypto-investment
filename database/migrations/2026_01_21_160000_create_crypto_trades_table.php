<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('coin_symbol'); // BTC, ETH, etc.
            $table->enum('trade_type', ['buy', 'sell'])->default('buy');
            $table->decimal('quantity', 20, 8); // High precision for crypto
            $table->decimal('price_usd', 20, 8);
            $table->decimal('total_amount', 20, 8);
            $table->decimal('fee_amount', 20, 8)->default(0);
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->decimal('net_amount', 20, 8);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->enum('order_type', ['market', 'limit', 'stop_loss'])->default('market');
            $table->decimal('limit_price', 20, 8)->nullable(); // For limit orders
            $table->decimal('stop_price', 20, 8)->nullable(); // For stop-loss orders
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'coin_symbol']);
            $table->index('status');
            $table->index('order_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto_trades');
    }
}
