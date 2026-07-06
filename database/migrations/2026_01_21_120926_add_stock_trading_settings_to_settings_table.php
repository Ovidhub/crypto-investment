<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockTradingSettingsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('enable_stock_trading', 10)->default('on')->after('enable_crypto_staking');
            $table->text('twelvedata_api_key')->nullable()->after('enable_stock_trading');
            $table->string('stock_market_hours_enabled', 10)->default('on')->after('twelvedata_api_key');
            $table->integer('max_stock_watchlist_items')->default(50)->after('stock_market_hours_enabled');
            $table->decimal('stock_trade_fee_percent', 5, 2)->default(0.5)->after('max_stock_watchlist_items');
            $table->timestamp('last_stock_force_update_at')->nullable()->after('stock_trade_fee_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_stock_trading',
                'twelvedata_api_key',
                'stock_market_hours_enabled',
                'max_stock_watchlist_items',
                'stock_trade_fee_percent',
                'last_stock_force_update_at'
            ]);
        });
    }
}
