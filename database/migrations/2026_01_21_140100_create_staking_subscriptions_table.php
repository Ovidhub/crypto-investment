<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStakingSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staking_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('crypto_staking_plan_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_staked', 15, 2);
            $table->decimal('current_rewards', 15, 2)->default(0.00);
            $table->decimal('compounded_amount', 15, 2)->default(0.00)->comment('Auto-restaked rewards');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('apr_at_subscription', 5, 2)->comment('APR frozen at time of subscription');
            $table->enum('status', ['active', 'completed', 'cancelled', 'force_completed'])->default('active');
            $table->timestamp('last_reward_at')->nullable();
            $table->string('lock_type')->default('locked')->comment('locked/flexible');
            $table->boolean('is_compound_enabled')->default(false);
            $table->unsignedBigInteger('force_completed_by')->nullable()->comment('Admin ID if force completed');
            $table->timestamp('force_completed_at')->nullable();
            $table->text('force_complete_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['crypto_staking_plan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staking_subscriptions');
    }
}
