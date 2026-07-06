<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpertTradersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expert_traders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('min_capital', 15, 2)->default(0);
            $table->decimal('max_capital', 15, 2)->default(0);
            $table->integer('total_followers')->default(0);
            $table->integer('max_followers')->default(100);
            $table->string('duration')->default('30 Days');
            $table->integer('active_trades')->default(0);
            $table->integer('total_trades')->default(0);
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->decimal('total_profit', 15, 2)->default(0);
            $table->decimal('roi_percentage', 8, 2)->default(0);
            $table->enum('risk_level', ['Low', 'Medium', 'High'])->default('Medium');
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
        Schema::dropIfExists('expert_traders');
    }
}
