<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stock_symbol');
            $table->enum('trade_type', ['buy', 'sell'])->default('buy');
            $table->decimal('quantity', 16, 8);
            $table->decimal('price', 16, 4);
            $table->decimal('total_amount', 16, 4);
            $table->decimal('fee_amount', 16, 4)->default(0);
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->decimal('net_amount', 16, 4);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'stock_symbol']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_trades');
    }
}
