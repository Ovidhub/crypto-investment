<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoStakingPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_staking_plans', function (Blueprint $table) {
            $table->id();
            $table->string('coin_symbol');
            $table->foreign('coin_symbol')->references('coin_symbol')->on('crypto_prices')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('apr_percentage', 5, 2)->default(10.00)->comment('Annual Percentage Rate');
            $table->decimal('min_stake', 15, 2)->default(100.00);
            $table->decimal('max_stake', 15, 2)->default(10000.00);
            $table->decimal('max_total_staked', 20, 2)->nullable()->comment('Maximum total staked across all users');
            $table->decimal('current_total_staked', 20, 2)->default(0.00)->comment('Current total staked by all users');
            $table->string('lock_duration')->default('30 Days')->comment('7 Days, 30 Days, 90 Days, 180 Days, Flexible');
            $table->string('reward_interval')->default('Daily')->comment('5 Minutes, 30 Minutes, Hourly, Daily, Weekly, Monthly');
            $table->decimal('early_withdrawal_penalty', 5, 2)->default(10.00)->comment('Penalty percentage on rewards for early withdrawal');
            $table->string('compound_enabled')->default('off')->comment('on/off - allow auto-restaking rewards');
            $table->string('auto_distribute')->default('on')->comment('on/off - enable automatic reward distribution');
            $table->string('distribution_paused')->default('off')->comment('on/off - emergency pause for this plan only');
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('crypto_staking_plans');
    }
}
