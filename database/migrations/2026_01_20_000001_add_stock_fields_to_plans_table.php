<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockFieldsToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->enum('plan_type', ['investment', 'stock'])->default('investment')->after('id');
            $table->string('stock_symbol')->nullable()->after('plan_type')->comment('Trading symbol e.g. NASDAQ:AAPL, NYSE:TSLA');
            $table->string('stock_company')->nullable()->after('stock_symbol')->comment('Company name e.g. Apple Inc');
            $table->text('stock_description')->nullable()->after('stock_company')->comment('Description of the stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['plan_type', 'stock_symbol', 'stock_company', 'stock_description']);
        });
    }
}
