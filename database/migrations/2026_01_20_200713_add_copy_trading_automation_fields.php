<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCopyTradingAutomationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expert_traders', function (Blueprint $table) {
            $table->string('distribution_interval')->default('Daily')->after('duration');
            $table->decimal('distribution_percentage', 5, 2)->nullable()->after('distribution_interval');
            $table->string('auto_distribute')->default('off')->after('distribution_percentage');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('copy_trading_auto_distribute')->default('off')->after('weekend_trade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expert_traders', function (Blueprint $table) {
            $table->dropColumn(['distribution_interval', 'distribution_percentage', 'auto_distribute']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('copy_trading_auto_distribute');
        });
    }
}
