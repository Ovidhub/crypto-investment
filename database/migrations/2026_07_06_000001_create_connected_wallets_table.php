<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectedWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('connected_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider');            // MetaMask, Trust Wallet, Coinbase, etc.
            $table->string('network')->nullable(); // Ethereum, BSC, etc. (optional)
            $table->string('address');             // PUBLIC wallet address only
            $table->string('label')->nullable();   // user friendly label
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('connected_wallets');
    }
}
