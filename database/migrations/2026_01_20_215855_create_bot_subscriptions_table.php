<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('trading_bot_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_invested', 15, 2);
            $table->decimal('current_profit', 15, 2)->default(0.00);
            $table->decimal('manual_profit_adjustment', 15, 2)->default(0.00);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('duration'); // 30 Days, 60 Days, 90 Days, etc.
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->timestamp('last_trade_at')->nullable();
            $table->timestamp('last_manual_update_at')->nullable();
            $table->unsignedBigInteger('last_manual_update_by')->nullable();
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
        Schema::dropIfExists('bot_subscriptions');
    }
}
