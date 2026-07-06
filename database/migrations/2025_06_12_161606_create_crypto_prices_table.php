<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('crypto_prices', function (Blueprint $table) {
            $table->id();
            $table->string('coin_symbol')->unique(); // e.g., BTC
            $table->string('name'); // e.g., Bitcoin
            $table->string('logo')->nullable(); // URL to logo
            $table->decimal('price_usd', 20, 8)->default(0);
            $table->decimal('percent_change_24h', 10, 4)->default(0);
            $table->decimal('market_cap', 25, 2)->default(0);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_prices');
    }
};
