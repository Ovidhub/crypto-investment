<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingBotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('strategy_type')->default('Day Trading'); // Scalping, Day Trading, Swing Trading
            $table->string('photo')->nullable();
            $table->decimal('win_rate', 5, 2)->default(70.00); // 60-90%
            $table->decimal('roi_percentage', 5, 2)->default(30.00); // Expected monthly ROI
            $table->integer('total_trades')->default(0);
            $table->decimal('total_profit', 15, 2)->default(0.00);
            $table->decimal('min_capital', 15, 2)->default(100.00);
            $table->decimal('max_capital', 15, 2)->default(10000.00);
            $table->string('auto_trade')->default('on'); // on/off
            $table->string('trade_interval')->default('Daily'); // 5 Minutes, 30 Minutes, Hourly, Daily
            $table->decimal('min_profit', 5, 2)->default(0.5); // Min profit % for winning trades
            $table->decimal('max_profit', 5, 2)->default(5.0); // Max profit % for winning trades
            $table->decimal('min_loss', 5, 2)->default(0.3); // Min loss % for losing trades
            $table->decimal('max_loss', 5, 2)->default(3.0); // Max loss % for losing trades
            $table->string('status')->default('active'); // active/inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trading_bots');
    }
}
