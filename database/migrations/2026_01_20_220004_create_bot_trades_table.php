<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('trading_bot_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('asset_symbol'); // BTC, ETH, BNB
            $table->string('asset_name'); // Bitcoin, Ethereum
            $table->string('asset_logo')->nullable(); // URL/path to crypto logo
            $table->decimal('entry_price', 20, 8);
            $table->decimal('exit_price', 20, 8);
            $table->decimal('amount', 15, 2); // Invested portion
            $table->decimal('profit_loss', 15, 2); // Calculated result
            $table->decimal('profit_percentage', 10, 4); // %
            $table->enum('outcome', ['win', 'loss']);
            $table->enum('trade_type', ['automated', 'manual'])->default('automated');
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->text('notes')->nullable(); // Admin notes for manual trades
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_trades');
    }
}
