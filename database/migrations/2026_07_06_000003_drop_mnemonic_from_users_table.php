<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the insecure recovery-phrase (mnemonic) column. The wallet-connect
 * feature now stores only PUBLIC addresses in the connected_wallets table.
 */
class DropMnemonicFromUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mnemonic')) {
                $table->dropColumn('mnemonic');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mnemonic')) {
                $table->text('mnemonic')->nullable();
            }
        });
    }
}
