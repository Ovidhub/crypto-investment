<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletDailyRewardToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'wallet_daily_reward')) {
                $table->decimal('wallet_daily_reward', 15, 2)->default(0)->after('wallet_type');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'wallet_daily_reward')) {
                $table->dropColumn('wallet_daily_reward');
            }
        });
    }
}
