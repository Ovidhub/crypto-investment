<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CryptoTrade;
use App\Models\CryptoPrice;
use App\Models\User;
use App\Models\Tp_Transaction;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CryptoTradingController extends Controller
{
    /**
     * Display all crypto trades with DataTables
     */
    public function index()
    {
        $trades = CryptoTrade::with(['user', 'cryptoPrice'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $users = User::orderBy('name')->get();
        
        return view('admin.crypto-trading.index', compact('trades', 'users'));
    }

    /**
     * Display specific user's crypto trades
     */
    public function userTrades($userId)
    {
        $user = User::findOrFail($userId);
        
        $trades = CryptoTrade::with('cryptoPrice')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate portfolio stats
        $completedTrades = CryptoTrade::where('user_id', $userId)
            ->where('status', 'completed')
            ->get();
        
        $totalInvested = $completedTrades->where('trade_type', 'buy')->sum('net_amount');
        $totalSold = $completedTrades->where('trade_type', 'sell')->sum('net_amount');
        
        // Calculate current holdings value
        $holdings = [];
        foreach ($completedTrades as $trade) {
            $symbol = $trade->coin_symbol;
            if (!isset($holdings[$symbol])) {
                $holdings[$symbol] = [
                    'quantity' => 0,
                    'total_cost' => 0
                ];
            }
            
            if ($trade->trade_type === 'buy') {
                $holdings[$symbol]['quantity'] += $trade->quantity;
                $holdings[$symbol]['total_cost'] += $trade->net_amount;
            } else {
                $holdings[$symbol]['quantity'] -= $trade->quantity;
                $holdings[$symbol]['total_cost'] -= $trade->net_amount;
            }
        }
        
        // Filter out zero positions and calculate current value
        $currentValue = 0;
        $holdingsCount = 0;
        foreach ($holdings as $symbol => $data) {
            if ($data['quantity'] > 0) {
                $crypto = CryptoPrice::where('coin_symbol', $symbol)->first();
                if ($crypto) {
                    $currentValue += $data['quantity'] * $crypto->price_usd;
                    $holdingsCount++;
                }
            }
        }
        
        $profitLoss = $currentValue - ($totalInvested - $totalSold);
        
        return view('admin.crypto-trading.user-trades', compact(
            'user',
            'trades',
            'totalInvested',
            'currentValue',
            'profitLoss',
            'holdingsCount'
        ));
    }

    /**
     * Display user's crypto holdings breakdown
     */
    public function userHoldings($userId)
    {
        $user = User::findOrFail($userId);
        
        $completedTrades = CryptoTrade::where('user_id', $userId)
            ->where('status', 'completed')
            ->get();
        
        $holdings = [];
        foreach ($completedTrades as $trade) {
            $symbol = $trade->coin_symbol;
            if (!isset($holdings[$symbol])) {
                $holdings[$symbol] = [
                    'quantity' => 0,
                    'total_cost' => 0,
                    'trades_count' => 0
                ];
            }
            
            if ($trade->trade_type === 'buy') {
                $holdings[$symbol]['quantity'] += $trade->quantity;
                $holdings[$symbol]['total_cost'] += $trade->net_amount;
            } else {
                $holdings[$symbol]['quantity'] -= $trade->quantity;
                $holdings[$symbol]['total_cost'] -= $trade->net_amount;
            }
            $holdings[$symbol]['trades_count']++;
        }
        
        // Build detailed holdings array with current prices
        $detailedHoldings = [];
        $totalPortfolioValue = 0;
        
        foreach ($holdings as $symbol => $data) {
            if ($data['quantity'] > 0.00000001) { // Filter out zero positions
                $crypto = CryptoPrice::where('coin_symbol', $symbol)->first();
                
                if ($crypto) {
                    $currentValue = $data['quantity'] * $crypto->price_usd;
                    $totalPortfolioValue += $currentValue;
                    
                    $detailedHoldings[] = [
                        'symbol' => $symbol,
                        'name' => $crypto->name,
                        'logo' => $crypto->logo,
                        'quantity' => $data['quantity'],
                        'total_cost' => $data['total_cost'],
                        'average_cost' => $data['total_cost'] / $data['quantity'],
                        'current_price' => $crypto->price_usd,
                        'current_value' => $currentValue,
                        'profit_loss' => $currentValue - $data['total_cost'],
                        'profit_loss_percent' => $data['total_cost'] > 0 
                            ? (($currentValue - $data['total_cost']) / $data['total_cost']) * 100 
                            : 0,
                        'trades_count' => $data['trades_count'],
                        'price_change_24h' => $crypto->percent_change_24h
                    ];
                }
            }
        }
        
        // Add concentration percentage
        foreach ($detailedHoldings as &$holding) {
            $holding['concentration'] = $totalPortfolioValue > 0 
                ? ($holding['current_value'] / $totalPortfolioValue) * 100 
                : 0;
        }
        
        // Sort by current value descending
        usort($detailedHoldings, function($a, $b) {
            return $b['current_value'] <=> $a['current_value'];
        });
        
        return view('admin.crypto-trading.user-holdings', compact('user', 'detailedHoldings', 'totalPortfolioValue'));
    }

    /**
     * Show create manual trade form
     */
    public function createTrade()
    {
        $users = User::orderBy('name')->get();
        $cryptos = CryptoPrice::orderBy('name')->get();
        $settings = Settings::first();
        
        return view('admin.crypto-trading.create-trade', compact('users', 'cryptos', 'settings'));
    }

    /**
     * Store manual trade
     */
    public function storeTrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'coin_symbol' => 'required|exists:crypto_prices,coin_symbol',
            'trade_type' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'price_usd' => 'required|numeric|min:0.01',
            'fee_percent' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->user_id);
            $crypto = CryptoPrice::where('coin_symbol', $request->coin_symbol)->firstOrFail();
            $settings = Settings::first();
            
            $quantity = floatval($request->quantity);
            $price = floatval($request->price_usd);
            $totalAmount = $quantity * $price;
            
            $feePercent = $request->fee_percent ?? $settings->crypto_trade_fee_percent ?? 0.25;
            $feeAmount = ($totalAmount * $feePercent) / 100;
            $netAmount = $totalAmount + $feeAmount;
            
            // Create trade record
            $trade = CryptoTrade::create([
                'user_id' => $request->user_id,
                'coin_symbol' => $request->coin_symbol,
                'trade_type' => $request->trade_type,
                'quantity' => $quantity,
                'price_usd' => $price,
                'total_amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'fee_percent' => $feePercent,
                'net_amount' => $netAmount,
                'status' => 'completed',
                'order_type' => 'market',
                'notes' => $request->notes,
                'completed_at' => now()
            ]);
            
            // Update user balance
            if ($request->trade_type === 'buy') {
                $user->account_bal -= $netAmount;
                $transactionType = 'Crypto Purchase';
                $transactionNarration = "Manual crypto purchase: {$quantity} {$crypto->name} @ $" . number_format($price, 2);
            } else {
                $user->account_bal += $netAmount;
                $transactionType = 'Crypto Sale';
                $transactionNarration = "Manual crypto sale: {$quantity} {$crypto->name} @ $" . number_format($price, 2);
            }
            $user->save();
            
            // Create transaction record
            Tp_Transaction::create([
                'user' => $user->id,
                'amount' => $netAmount,
                'type' => $transactionType,
                'narration' => $transactionNarration
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('admin.crypto-trading.user-trades', $user->id)
                ->with('success', 'Manual crypto trade created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to create trade: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show edit trade form
     */
    public function editTrade($id)
    {
        $trade = CryptoTrade::with(['user', 'cryptoPrice'])->findOrFail($id);
        $cryptos = CryptoPrice::orderBy('name')->get();
        $settings = Settings::first();
        
        return view('admin.crypto-trading.edit-trade', compact('trade', 'cryptos', 'settings'));
    }

    /**
     * Update trade with balance reconciliation
     */
    public function updateTrade(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'coin_symbol' => 'required|exists:crypto_prices,coin_symbol',
            'trade_type' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'price_usd' => 'required|numeric|min:0.01',
            'fee_percent' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $trade = CryptoTrade::findOrFail($id);
            $user = $trade->user;
            $oldNetAmount = $trade->net_amount;
            $oldTradeType = $trade->trade_type;
            $oldStatus = $trade->status;
            
            // Calculate new amounts
            $quantity = floatval($request->quantity);
            $price = floatval($request->price_usd);
            $totalAmount = $quantity * $price;
            
            $settings = Settings::first();
            $feePercent = $request->fee_percent ?? $settings->crypto_trade_fee_percent ?? 0.25;
            $feeAmount = ($totalAmount * $feePercent) / 100;
            $newNetAmount = $totalAmount + $feeAmount;
            
            // Reverse old balance impact if trade was completed
            if ($oldStatus === 'completed') {
                if ($oldTradeType === 'buy') {
                    $user->account_bal += $oldNetAmount; // Refund old purchase
                } else {
                    $user->account_bal -= $oldNetAmount; // Reverse old sale
                }
            }
            
            // Apply new balance impact if new status is completed
            if ($request->status === 'completed') {
                if ($request->trade_type === 'buy') {
                    $user->account_bal -= $newNetAmount; // Deduct new purchase
                } else {
                    $user->account_bal += $newNetAmount; // Add new sale
                }
            }
            
            $user->save();
            
            // Update trade record
            $trade->update([
                'coin_symbol' => $request->coin_symbol,
                'trade_type' => $request->trade_type,
                'quantity' => $quantity,
                'price_usd' => $price,
                'total_amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'fee_percent' => $feePercent,
                'net_amount' => $newNetAmount,
                'status' => $request->status,
                'notes' => $request->notes,
                'completed_at' => $request->status === 'completed' ? ($trade->completed_at ?? now()) : null
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('admin.crypto-trading.user-trades', $user->id)
                ->with('success', 'Trade updated successfully with balance reconciliation');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to update trade: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete trade and reverse balance impact
     */
    public function deleteTrade($id)
    {
        DB::beginTransaction();
        try {
            $trade = CryptoTrade::findOrFail($id);
            $user = $trade->user;
            
            // Reverse balance impact if trade was completed
            if ($trade->status === 'completed') {
                if ($trade->trade_type === 'buy') {
                    $user->account_bal += $trade->net_amount; // Refund purchase
                } else {
                    $user->account_bal -= $trade->net_amount; // Reverse sale
                }
                $user->save();
                
                // Create reversal transaction record
                Tp_Transaction::create([
                    'user' => $user->id,
                    'amount' => $trade->net_amount,
                    'type' => 'Crypto Trade Reversal',
                    'narration' => "Trade deleted: {$trade->trade_type} {$trade->quantity} {$trade->coin_symbol}"
                ]);
            }
            
            $trade->delete();
            
            DB::commit();
            
            return back()->with('success', 'Trade deleted and balance reversed successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete trade: ' . $e->getMessage());
        }
    }

    /**
     * Display crypto prices management
     */
    public function manageCryptos()
    {
        $cryptos = CryptoPrice::orderBy('name')->get();
        
        return view('admin.crypto-trading.manage-cryptos', compact('cryptos'));
    }

    /**
     * Show edit crypto form
     */
    public function editCrypto($id)
    {
        $crypto = CryptoPrice::findOrFail($id);
        
        return view('admin.crypto-trading.edit-crypto', compact('crypto'));
    }

    /**
     * Update crypto
     */
    public function updateCrypto(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'coin_symbol' => 'required|string|max:20|unique:crypto_prices,coin_symbol,' . $id,
            'name' => 'required|string|max:100',
            'logo' => 'nullable|url',
            'price_usd' => 'required|numeric|min:0',
            'percent_change_24h' => 'nullable|numeric',
            'market_cap' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $crypto = CryptoPrice::findOrFail($id);
            $crypto->update($request->all());
            
            return redirect()
                ->route('admin.crypto-trading.manage-cryptos')
                ->with('success', 'Crypto updated successfully');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update crypto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show create crypto form
     */
    public function createCrypto()
    {
        return view('admin.crypto-trading.create-crypto');
    }

    /**
     * Store new crypto
     */
    public function storeCrypto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coin_symbol' => 'required|string|max:20|unique:crypto_prices,coin_symbol',
            'name' => 'required|string|max:100',
            'logo' => 'nullable|url',
            'price_usd' => 'required|numeric|min:0',
            'percent_change_24h' => 'nullable|numeric',
            'market_cap' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            CryptoPrice::create($request->all());
            
            return redirect()
                ->route('admin.crypto-trading.manage-cryptos')
                ->with('success', 'Crypto added successfully');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to add crypto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete crypto
     */
    public function deleteCrypto($id)
    {
        try {
            $crypto = CryptoPrice::findOrFail($id);
            
            // Check if crypto has active trades
            $tradesCount = CryptoTrade::where('coin_symbol', $crypto->coin_symbol)->count();
            
            if ($tradesCount > 0) {
                return back()->with('error', 'Cannot delete crypto with existing trades');
            }
            
            $crypto->delete();
            
            return back()->with('success', 'Crypto deleted successfully');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete crypto: ' . $e->getMessage());
        }
    }
}
