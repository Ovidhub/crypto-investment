<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStakingControlsToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('enable_crypto_staking', 10)->default('on')->after('enable_bot_trading');
            $table->string('pause_all_staking_distributions', 10)->default('off')->after('enable_crypto_staking');
            $table->string('show_staking_leaderboard', 10)->default('on')->after('pause_all_staking_distributions');
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
            $table->dropColumn(['enable_crypto_staking', 'pause_all_staking_distributions', 'show_staking_leaderboard']);
        });
    }
}
