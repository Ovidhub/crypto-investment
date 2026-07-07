<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveChatTables extends Migration
{
    public function up()
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();     // visitor identifier (stored in their browser)
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // if a logged-in customer
            $table->string('page_url')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->enum('sender', ['visitor', 'admin']);
            $table->text('body');
            $table->boolean('read_by_admin')->default(false);
            $table->boolean('read_by_visitor')->default(false);
            $table->timestamps();
        });

        Schema::create('chat_agent_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('appear_offline')->default(false); // admin manual override
            $table->timestamp('last_seen_at')->nullable();     // heartbeat while inbox is open
            $table->timestamps();
        });

        // single status row
        \Illuminate\Support\Facades\DB::table('chat_agent_status')->insert([
            'id' => 1,
            'appear_offline' => false,
            'last_seen_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('chat_agent_status');
    }
}
