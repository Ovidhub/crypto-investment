<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique();
            $table->string('name');
            $table->string('exchange')->nullable();
            $table->string('logo_url')->nullable();
            $table->decimal('price', 16, 4)->default(0);
            $table->decimal('open', 16, 4)->nullable();
            $table->decimal('high', 16, 4)->nullable();
            $table->decimal('low', 16, 4)->nullable();
            $table->decimal('previous_close', 16, 4)->nullable();
            $table->decimal('change', 16, 4)->default(0);
            $table->decimal('percent_change', 10, 4)->default(0);
            $table->bigInteger('volume')->default(0);
            $table->decimal('market_cap', 25, 2)->nullable();
            $table->decimal('pe_ratio', 10, 2)->nullable();
            $table->decimal('dividend_yield', 8, 4)->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
            
            $table->index(['symbol', 'exchange']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_prices');
    }
}
