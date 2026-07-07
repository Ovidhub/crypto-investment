<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpCountryToChatConversations extends Migration
{
    public function up()
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_conversations', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('chat_conversations', 'country')) {
                $table->string('country', 80)->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('chat_conversations', 'country_code')) {
                $table->string('country_code', 2)->nullable()->after('country');
            }
        });
    }

    public function down()
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'country', 'country_code']);
        });
    }
}
