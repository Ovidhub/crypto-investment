<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMathCaptchaToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('settings', 'math_captcha')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('math_captcha', 10)->default('true')->after('captcha');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('settings', 'math_captcha')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('math_captcha');
            });
        }
    }
}
