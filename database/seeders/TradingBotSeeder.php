<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TradingBot;

class TradingBotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bots = [
            [
                'name' => 'Quantum Trader AI',
                'description' => 'Advanced AI-powered bot using quantum computing algorithms for ultra-fast trade execution. Specializes in volatile markets with high-frequency trading strategies.',
                'strategy_type' => 'Scalping',
                'win_rate' => 82.50,
                'roi_percentage' => 45.20,
                'total_trades' => 1523,
                'total_profit' => 28450.75,
                'min_capital' => 100,
                'max_capital' => 10000,
                'auto_trade' => 'on',
                'trade_interval' => '5 Minutes',
                'min_profit' => 0.8,
                'max_profit' => 4.5,
                'min_loss' => 0.3,
                'max_loss' => 2.1,
                'status' => 'active',
            ],
            [
                'name' => 'Smart Swing Pro',
                'description' => 'Professional swing trading bot that capitalizes on medium-term price movements. Perfect for hands-off investors seeking steady returns.',
                'strategy_type' => 'Swing Trading',
                'win_rate' => 76.30,
                'roi_percentage' => 38.90,
                'total_trades' => 892,
                'total_profit' => 19230.50,
                'min_capital' => 500,
                'max_capital' => 50000,
                'auto_trade' => 'on',
                'trade_interval' => 'Daily',
                'min_profit' => 1.2,
                'max_profit' => 6.8,
                'min_loss' => 0.5,
                'max_loss' => 3.2,
                'status' => 'active',
            ],
            [
                'name' => 'DayTrade Master',
                'description' => 'Intraday trading specialist focusing on capturing daily price volatility. Uses technical indicators and pattern recognition for optimal entry and exit points.',
                'strategy_type' => 'Day Trading',
                'win_rate' => 79.10,
                'roi_percentage' => 42.60,
                'total_trades' => 1147,
                'total_profit' => 23670.90,
                'min_capital' => 250,
                'max_capital' => 25000,
                'auto_trade' => 'on',
                'trade_interval' => 'Hourly',
                'min_profit' => 1.0,
                'max_profit' => 5.5,
                'min_loss' => 0.4,
                'max_loss' => 2.5,
                'status' => 'active',
            ],
            [
                'name' => 'Crypto Scalper Elite',
                'description' => 'Lightning-fast scalping bot designed for cryptocurrency markets. Executes hundreds of small profitable trades throughout the day.',
                'strategy_type' => 'Scalping',
                'win_rate' => 85.70,
                'roi_percentage' => 51.30,
                'total_trades' => 2341,
                'total_profit' => 35890.25,
                'min_capital' => 100,
                'max_capital' => 15000,
                'auto_trade' => 'on',
                'trade_interval' => '5 Minutes',
                'min_profit' => 0.5,
                'max_profit' => 3.2,
                'min_loss' => 0.2,
                'max_loss' => 1.8,
                'status' => 'active',
            ],
            [
                'name' => 'Neural Network Bot',
                'description' => 'Deep learning bot trained on millions of historical trades. Adapts to market conditions in real-time using advanced neural network architecture.',
                'strategy_type' => 'Day Trading',
                'win_rate' => 80.40,
                'roi_percentage' => 47.80,
                'total_trades' => 1689,
                'total_profit' => 31240.60,
                'min_capital' => 300,
                'max_capital' => 30000,
                'auto_trade' => 'on',
                'trade_interval' => '30 Minutes',
                'min_profit' => 0.9,
                'max_profit' => 5.0,
                'min_loss' => 0.3,
                'max_loss' => 2.3,
                'status' => 'active',
            ],
            [
                'name' => 'Momentum Hunter',
                'description' => 'Momentum-based trading bot that identifies and rides strong price trends. Excels in trending markets with clear directional movement.',
                'strategy_type' => 'Swing Trading',
                'win_rate' => 74.20,
                'roi_percentage' => 36.50,
                'total_trades' => 734,
                'total_profit' => 17890.40,
                'min_capital' => 500,
                'max_capital' => 40000,
                'auto_trade' => 'on',
                'trade_interval' => 'Daily',
                'min_profit' => 1.5,
                'max_profit' => 7.2,
                'min_loss' => 0.6,
                'max_loss' => 3.5,
                'status' => 'active',
            ],
            [
                'name' => 'Arbitrage Alpha',
                'description' => 'Specialized arbitrage bot that exploits price differences across multiple exchanges. Low-risk strategy with consistent returns.',
                'strategy_type' => 'Scalping',
                'win_rate' => 88.60,
                'roi_percentage' => 32.10,
                'total_trades' => 3127,
                'total_profit' => 27450.80,
                'min_capital' => 200,
                'max_capital' => 20000,
                'auto_trade' => 'on',
                'trade_interval' => '5 Minutes',
                'min_profit' => 0.3,
                'max_profit' => 2.1,
                'min_loss' => 0.1,
                'max_loss' => 1.2,
                'status' => 'active',
            ],
            [
                'name' => 'Trend Surfer Pro',
                'description' => 'Long-term trend following bot with sophisticated risk management. Ideal for patients investors seeking substantial gains over time.',
                'strategy_type' => 'Swing Trading',
                'win_rate' => 71.90,
                'roi_percentage' => 41.70,
                'total_trades' => 567,
                'total_profit' => 21560.30,
                'min_capital' => 1000,
                'max_capital' => 100000,
                'auto_trade' => 'on',
                'trade_interval' => 'Daily',
                'min_profit' => 2.0,
                'max_profit' => 9.5,
                'min_loss' => 0.8,
                'max_loss' => 4.2,
                'status' => 'active',
            ],
        ];

        foreach ($bots as $bot) {
            TradingBot::create($bot);
        }
        
        $this->command->info('8 sample trading bots created successfully!');
    }
}

