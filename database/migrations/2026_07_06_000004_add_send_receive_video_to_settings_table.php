<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendReceiveVideoToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'send_receive_video')) {
                // TEXT (not VARCHAR) to avoid the settings table's 65535 row-size limit.
                $table->text('send_receive_video')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'send_receive_video')) {
                $table->dropColumn('send_receive_video');
            }
        });
    }
}
