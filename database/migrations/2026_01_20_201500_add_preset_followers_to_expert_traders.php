<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresetFollowersToExpertTraders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expert_traders', function (Blueprint $table) {
            $table->integer('preset_followers')->default(0)->after('total_followers');
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
            $table->dropColumn('preset_followers');
        });
    }
}
