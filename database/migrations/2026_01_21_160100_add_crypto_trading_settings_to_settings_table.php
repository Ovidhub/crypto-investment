<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCryptoTradingSettingsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->enum('enable_crypto_trading', ['on', 'off'])->default('on')->after('enable_stock_trading');
            $table->decimal('crypto_trade_fee_percent', 5, 2)->default(0.25)->after('enable_crypto_trading');
            $table->decimal('min_crypto_trade_amount', 10, 2)->default(10.00)->after('crypto_trade_fee_percent');
            $table->integer('max_crypto_watchlist_items')->default(50)->after('min_crypto_trade_amount');
        });

        // Update existing settings record if it exists
        DB::table('settings')->limit(1)->update([
            'enable_crypto_trading' => 'on',
            'crypto_trade_fee_percent' => 0.25,
            'min_crypto_trade_amount' => 10.00,
            'max_crypto_watchlist_items' => 50
        ]);
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
                'enable_crypto_trading',
                'crypto_trade_fee_percent',
                'min_crypto_trade_amount',
                'max_crypto_watchlist_items'
            ]);
        });
    }
}
