<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\User\ViewsController;
use App\Http\Controllers\User\WithdrawalController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\DepositController;
use App\Http\Controllers\User\PaystackController;
use App\Http\Controllers\User\UserSubscriptionController;
use App\Http\Controllers\User\UserInvPlanController;
use App\Http\Controllers\User\VerifyController;
use App\Http\Controllers\User\SomeController;
use App\Http\Controllers\User\SocialLoginController;
use App\Http\Controllers\User\ExchangeController;
use App\Http\Controllers\User\FlutterwaveController;
use App\Http\Controllers\User\MembershipController;
use App\Http\Controllers\User\TransferController;
use App\Http\Controllers\User\VerificationCodeController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\TradingController;
use App\Http\Controllers\User\StockInvestmentController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\InternalRemitController;
use App\Http\Controllers\User\LoanController;
use App\Http\Controllers\User\SignalController;
use Illuminate\Support\Facades\Route;


// Email verification routes
Route::get('/verify-email', 'App\Http\Controllers\User\UsersController@verifyemail')->middleware('auth')->name('verification.notice');

// New verification code routes - Rate limited to prevent brute force
Route::middleware(['auth', 'throttle:5,1'])->group(function () {
	Route::get('/verify-code', [VerificationCodeController::class, 'show'])->name('verification.code');
	Route::post('/verify-code', [VerificationCodeController::class, 'verify'])->name('verification.verify-code');
	Route::post('/email/verification-code', [VerificationCodeController::class, 'send'])->name('verification.send-code');
});

// Comment out or remove the old verification route
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();
//     return redirect('/dashboard');
// })->middleware(['auth', 'signed'])->name('verification.verify');

// Modify this route to resend verification codes instead of links
Route::post('/email/verification-notification', function (Request $request) {
	$request->user()->sendEmailVerificationNotification();
	return back()->with('message', 'Verification code sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// Socialite login - Rate limited to prevent authentication abuse
Route::middleware(['throttle:10,1'])->group(function () {
	Route::get('/auth/{social}/redirect', [SocialLoginController::class, 'redirect'])->where('social', 'twitter|facebook|linkedin|google|github|bitbucket')->name('social.redirect');
	Route::get('/auth/{social}/callback', [SocialLoginController::class, 'authenticate'])->where('social', 'twitter|facebook|linkedin|google|github|bitbucket')->name('social.callback');
});

Route::get('/ref/{id}', 'App\Http\Controllers\Controller@ref')->name('ref');

/*    Dashboard and user features routes  */
// Views routes
Route::middleware(['auth:sanctum', 'verified', 'check.blocked', 'complete.kyc'])->get('/dashboard', [ViewsController::class, 'dashboard'])->name('dashboard');

Route::middleware(['auth:sanctum', 'verified', 'check.blocked'])->prefix('dashboard')->group(function () {

	// Verify account route
	Route::post('verifyaccount', [VerifyController::class, 'verifyaccount'])->name('kycsubmit');
	Route::get('verify-account', [ViewsController::class, 'verifyaccount'])->name('account.verify');
	Route::get('kyc-form', [ViewsController::class, 'verificationForm'])->name('kycform');
	Route::get('support', [ViewsController::class, 'support'])->name('support');

	Route::middleware('complete.kyc')->group(function () {
		Route::get('account-settings', [ViewsController::class, 'profile'])->name('profile');
		Route::get('accountdetails', [ViewsController::class, 'accountdetails'])->name('accountdetails');
		Route::get('notification', [ViewsController::class, 'notification'])->name('notification');

		Route::get('deposits', [ViewsController::class, 'deposits'])->name('deposits');
		Route::get('skip_account', [ViewsController::class, 'skip_account']);

		Route::get('tradinghistory', [ViewsController::class, 'tradinghistory'])->name('tradinghistory');
		Route::get('accounthistory', [ViewsController::class, 'accounthistory'])->name('accounthistory');
		Route::get('withdrawals', [ViewsController::class, 'withdrawals'])->name('withdrawalsdeposits')->middleware(['password.confirm']);
		Route::get('subtrade', [ViewsController::class, 'subtrade'])->name('subtrade');
		Route::get('buy-plan', [ViewsController::class, 'mplans'])->name('mplans');
		Route::get('stock-plans', [StockInvestmentController::class, 'index'])->name('stockplans');
		Route::post('stock-plans/purchase', [StockInvestmentController::class, 'purchase'])->name('stockplans.purchase');
		
		// Copy Trading Routes
		Route::get('copy-trading', [App\Http\Controllers\User\CopyTradingController::class, 'index'])->name('copytrading');
		Route::post('copy-trading/subscribe', [App\Http\Controllers\User\CopyTradingController::class, 'subscribe'])->name('copytrading.subscribe');
		Route::get('copy-trading/subscriptions', [App\Http\Controllers\User\CopyTradingController::class, 'mySubscriptions'])->name('copytrading.subscriptions');
		Route::post('copy-trading/cancel/{id}', [App\Http\Controllers\User\CopyTradingController::class, 'cancelSubscription'])->name('copytrading.cancel');
		
		// Bot Trading Routes
		Route::get('bot-trading', [App\Http\Controllers\User\BotTradingController::class, 'index'])->name('user.bot-trading.index');
		Route::get('bot-trading/{bot}', [App\Http\Controllers\User\BotTradingController::class, 'show'])->name('user.bot-trading.show');
		Route::post('bot-trading/subscribe/{bot}', [App\Http\Controllers\User\BotTradingController::class, 'subscribe'])->name('user.bot-trading.subscribe');
		Route::get('bot-trading-subscriptions', [App\Http\Controllers\User\BotTradingController::class, 'subscriptions'])->name('user.bot-trading.subscriptions');
		Route::get('bot-trading-trades', [App\Http\Controllers\User\BotTradingController::class, 'trades'])->name('user.bot-trading.trades');
		Route::post('bot-trading/cancel/{subscription}', [App\Http\Controllers\User\BotTradingController::class, 'cancel'])->name('user.bot-trading.cancel');
		
		// Crypto Staking Routes
		Route::get('crypto-staking', [App\Http\Controllers\User\CryptoStakingController::class, 'index'])->name('crypto-staking.index');
		Route::get('crypto-staking/{id}', [App\Http\Controllers\User\CryptoStakingController::class, 'show'])->name('crypto-staking.show');
		Route::post('crypto-staking/subscribe/{id}', [App\Http\Controllers\User\CryptoStakingController::class, 'subscribe'])->name('crypto-staking.subscribe');
		Route::get('crypto-staking-subscriptions', [App\Http\Controllers\User\CryptoStakingController::class, 'mySubscriptions'])->name('crypto-staking.subscriptions');
		Route::post('crypto-staking/cancel/{id}', [App\Http\Controllers\User\CryptoStakingController::class, 'cancel'])->name('crypto-staking.cancel');
		Route::get('crypto-staking-leaderboard', [App\Http\Controllers\User\CryptoStakingController::class, 'leaderboard'])->name('crypto-staking.leaderboard');
		
		// Stock Trading Routes
		Route::get('stock-trading', [App\Http\Controllers\User\StockTradingController::class, 'index'])->name('stock-trading.index');	Route::get('stock-trading/portfolio', [App\Http\Controllers\User\StockTradingController::class, 'portfolio'])->name('stock-trading.portfolio');		Route::post('stock-trading/order/place', [App\Http\Controllers\User\StockTradingController::class, 'placeOrder'])->name('stock-trading.order.place');
		Route::post('stock-trading/order/cancel/{orderId}', [App\Http\Controllers\User\StockTradingController::class, 'cancelOrder'])->name('stock-trading.order.cancel');
		Route::get('stock-trading/orders/open/{symbol?}', [App\Http\Controllers\User\StockTradingController::class, 'getOpenOrders'])->name('stock-trading.orders.open');
		Route::post('stock-trading/buy', [App\Http\Controllers\User\StockTradingController::class, 'buyStock'])->name('stock-trading.buy');
		Route::post('stock-trading/sell', [App\Http\Controllers\User\StockTradingController::class, 'sellStock'])->name('stock-trading.sell');
		Route::post('stock-trading/watchlist/add', [App\Http\Controllers\User\StockTradingController::class, 'addToWatchlist'])->name('stock-trading.watchlist.add');
		Route::delete('stock-trading/watchlist/{symbol}', [App\Http\Controllers\User\StockTradingController::class, 'removeFromWatchlist'])->name('stock-trading.watchlist.remove');
		Route::get('stock-trading/api/quote/{symbol}', [App\Http\Controllers\User\StockTradingController::class, 'getQuote'])->name('stock-trading.api.quote');
		Route::get('stock-trading/api/search', [App\Http\Controllers\User\StockTradingController::class, 'searchStocks'])->name('stock-trading.api.search');
		Route::get('stock-trading/{symbol}', [App\Http\Controllers\User\StockTradingController::class, 'show'])->name('stock-trading.show');
		
		// Crypto Trading Routes
		Route::get('crypto-trading', [App\Http\Controllers\User\CryptoTradingController::class, 'index'])->name('crypto-trading.index');
		Route::get('crypto-trading/portfolio', [App\Http\Controllers\User\CryptoTradingController::class, 'portfolio'])->name('crypto-trading.portfolio');
		Route::get('crypto-trading/orders', [App\Http\Controllers\User\CryptoTradingController::class, 'orders'])->name('crypto-trading.orders');
		Route::post('crypto-trading/order/place', [App\Http\Controllers\User\CryptoTradingController::class, 'placeOrder'])->name('crypto-trading.order.place');
		Route::post('crypto-trading/order/cancel/{orderId}', [App\Http\Controllers\User\CryptoTradingController::class, 'cancelOrder'])->name('crypto-trading.order.cancel');
		Route::post('crypto-trading/watchlist/add', [App\Http\Controllers\User\CryptoTradingController::class, 'addToWatchlist'])->name('crypto-trading.watchlist.add');
		Route::delete('crypto-trading/watchlist/{symbol}', [App\Http\Controllers\User\CryptoTradingController::class, 'removeFromWatchlist'])->name('crypto-trading.watchlist.remove');
		Route::get('crypto-trading/trade/{symbol}', [App\Http\Controllers\User\CryptoTradingController::class, 'trade'])->name('crypto-trading.trade');
		
		Route::get('myplans/{sort}', [ViewsController::class, 'myplans'])->name('myplans');
		Route::get('sort-plans/{sorttype}', [ViewsController::class, 'sortPlans'])->name('sortplans');

		Route::get('plan-details/{id}', [ViewsController::class, 'planDetails'])->name('plandetails');
		Route::get('cancel-plan/{id}', [UserInvPlanController::class, 'cancelPlan'])->name('cancelplan');

		Route::get('referuser', [ViewsController::class, 'referuser'])->name('referuser');
		
		// Wallet Connect Routes
        Route::get('/wallet-connect', [WalletController::class, 'index'])->name('wallet.connect');
        Route::post('/wallet-connect', [WalletController::class, 'store'])->name('wallet.store');
        Route::delete('/wallet-connect/{id}', [WalletController::class, 'destroy'])->name('wallet.destroy');
        
        Route::get('/transfer', [InternalRemitController::class, 'index'])->name('transfer');
        Route::post('/transfer', [InternalRemitController::class, 'processTransfer'])->name('transfer.process');
        


        // 1. The Route to show the History (Table)
        Route::get('/loan-history', [LoanController::class, 'index'])->name('loan.history');
    
        // 2. The Route to show the Application Form
        Route::get('/loan-application', [LoanController::class, 'create'])->name('loan.apply');
    
        // 3. The Route to save the application
        Route::post('/loan-application', [LoanController::class, 'store'])->name('loan.store');
        
        
        // Trading Routes
        Route::prefix('trade')->name('trade.')->controller(\App\Http\Controllers\User\TradeController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'single')->name('single');
            Route::get('/monitor/{tradeId}', 'monitor')->name('monitor');
            Route::get('/api/type/{type}', 'getByType')->name('api.type');
            Route::get('/api/search', 'search')->name('api.search');
        });
        
        
                Route::post('joinplan', [UserInvPlanController::class, 'joinplan'])->name('joinplan');
        Route::post('joininvestmentplan', [UserInvPlanController::class, 'joininvestmentplan'])->name('joininvestmentplan');
        
        

        
        Route::get('/trading-signals', [SignalController::class, 'index'])->name('signals.index');


		Route::get('manage-account-security', [ViewsController::class, 'twofa'])->name('twofa');
		Route::get('transfer-funds', [ViewsController::class, 'transferview'])->name('transferview');

		// Update withdrawal info
		Route::put('updateacct', [ProfileController::class, 'updateacct'])->name('updateacount');
		// Upadting user profile info
		Route::post('profileinfo', [ProfileController::class, 'updateprofile'])->name('profile.update');
		// Update password
		Route::put('updatepass', [ProfileController::class, 'updatepass'])->name('updateuserpass');

		// Update emal preference
		Route::put('update-email-preference', [ProfileController::class, 'updateemail'])->name('updateemail');

		// Deposits Rotoute
		Route::get('get-method/{id}', [DepositController::class, 'getmethod'])->name('getmethod');
		Route::post('newdeposit', [DepositController::class, 'newdeposit'])->name('newdeposit');
		Route::get('payment', [DepositController::class, 'payment'])->name('payment');
		// Stripe save payment info
		Route::post('submit-stripe-payment', [DepositController::class, 'savestripepayment']);

		// Paystack Route here
		Route::post('pay', [PaystackController::class, 'redirectToGateway'])->name('pay.paystack');
		Route::get('paystackcallback', [PaystackController::class, 'handleGatewayCallback']);
		Route::post('savedeposit', [DepositController::class, 'savedeposit'])->name('savedeposit');

		// Flutterwave Routes here
		Route::post('/payviaflutterwave', [FlutterwaveController::class, 'initialize'])->name('paybyflutterwave');
		// The callback url after a payment
		Route::get('/rave/callback', [FlutterwaveController::class, 'callback'])->name('callback');

		// Withdrawals
		Route::post('enter-amount', [WithdrawalController::class, 'withdrawamount'])->name('withdrawamount');
		Route::get('withdraw-funds', [WithdrawalController::class, 'withdrawfunds'])->name('withdrawfunds');
		Route::get('getotp', [WithdrawalController::class, 'getotp'])->name('getotp');
		Route::post('completewithdrawal', [WithdrawalController::class, 'completewithdrawal'])->name('completewithdrawal');

		// Subscription Trading
		Route::post('savemt4details', [UserSubscriptionController::class, 'savemt4details'])->name('savemt4details');
		Route::get('delsubtrade/{id}', [UserSubscriptionController::class, 'delsubtrade'])->name('delsubtrade');
		Route::get('renew/subscription/{id}', [UserSubscriptionController::class, 'renewSubscription'])->name('renewsub');

		// Investment, user buys plan
		Route::post('joinplan', [UserInvPlanController::class, 'joinplan'])->name('joinplan');

		Route::post('changetheme', [SomeController::class, 'changetheme'])->name('changetheme');

		Route::post('paypalverify/{amount}', 'App\Http\Controllers\Controller@paypalverify')->name('paypalverify');
		Route::get('cpay/{amount}/{coin}/{ui}/{msg}', 'App\Http\Controllers\Controller@cpay')->name('cpay');
		Route::get('asset-balance', [ExchangeController::class, 'assetview'])->name('assetbalance');
		Route::get('swap-history', [ExchangeController::class, 'history'])->name('swaphistory');

		Route::get('asset-price/{base}/{quote}/{amount}', [ExchangeController::class, 'getprice'])->name('getprice');
		Route::post('exchange', [ExchangeController::class, 'exchange'])->name('exchangenow');
		Route::get('balances/{coin}', [ExchangeController::class, 'getBalance'])->name('getbalance');

		// USer to User transfer
		Route::post('transfertouser', [TransferController::class, 'transfertouser'])->name('transfertouser');

		// binance crypto payments routes
		Route::get('/binance/success', [ViewsController::class, 'binanceSuccess'])->name('bsuccess');
		Route::get('/binance/error', [ViewsController::class, 'binanceError'])->name('berror');


		//membership route for user side
		Route::name('user.')->group(function () {
			Route::get('/courses', [MembershipController::class, 'courses'])->name('courses');
			Route::get('/course-details/{course}/{id}', [MembershipController::class, 'courseDetails'])->name('course.details');
			Route::post('/buy-course', [MembershipController::class, 'buyCourse'])->name('buycourse');
			Route::get('/my-courses', [MembershipController::class, 'myCourses'])->name('mycourses');
			Route::get('/course-details/{id}', [MembershipController::class, 'myCoursesDetails'])->name('mycoursedetails');
			Route::get('/learning/{lesson}/{course?}', [MembershipController::class, 'learning'])->name('learning');
		});

		//signals
		Route::get('/trade-signals', [ViewsController::class, 'tradeSignals'])->name('tsignals');
		Route::get('/renew-subscription', [TransferController::class, 'renewSignalSub'])->name('renewsignals');

		// Trading platform routes
		Route::get('trading', [TradingController::class, 'index'])->name('trading');
		Route::get('trading/crypto', [TradingController::class, 'crypto'])->name('trading.crypto');
		Route::get('trading/stocks', [TradingController::class, 'stocks'])->name('trading.stocks');
		Route::get('trading/crypto/{symbol}', [TradingController::class, 'showCrypto'])->name('trading.crypto.symbol');
		Route::get('trading/stocks/{symbol}', [TradingController::class, 'showStocks'])->name('trading.stocks.symbol');
		
		// Trading API endpoints for frontend
		Route::prefix('api/trading')->name('api.trading.')->group(function () {
			// Watchlist endpoints
			Route::get('watchlist', [TradingController::class, 'getWatchlist'])->name('watchlist');
			Route::post('watchlist/add', [TradingController::class, 'addToWatchlist'])->name('watchlist.add');
			Route::delete('watchlist/remove/{id}', [TradingController::class, 'removeFromWatchlist'])->name('watchlist.remove');
			
			// User preferences endpoints
			Route::get('preferences', [TradingController::class, 'getPreferences'])->name('preferences');
			Route::post('preferences', [TradingController::class, 'savePreferences'])->name('preferences.save');
			
			// Market data endpoints (proxy to external APIs)
			Route::get('market/crypto', [TradingController::class, 'getCryptoMarket'])->name('market.crypto');
			Route::get('market/stocks', [TradingController::class, 'getStockMarket'])->name('market.stocks');
			Route::get('symbol/crypto/{symbol}', [TradingController::class, 'getCryptoSymbol'])->name('symbol.crypto');
			Route::get('symbol/stocks/{symbol}', [TradingController::class, 'getStockSymbol'])->name('symbol.stocks');
			
			// New API endpoints for the crypto and stocks views
			Route::get('crypto/symbol', [TradingController::class, 'getCryptoSymbol'])->name('crypto.symbol');
			Route::get('crypto/candles', [TradingController::class, 'getCryptoCandles'])->name('crypto.candles');
			Route::get('crypto/top', [TradingController::class, 'getTopCryptos'])->name('crypto.top');
			Route::get('stocks/symbols', [TradingController::class, 'getStockSymbols'])->name('stocks.symbols');
			Route::get('stocks/quote/{symbol}', [TradingController::class, 'getStockQuoteFinnhub'])->name('stocks.quote');
			
			// Order endpoints
			Route::post('order/create', [TradingController::class, 'createOrder'])->name('order.create');
			Route::get('orders', [TradingController::class, 'getOrders'])->name('orders');
			Route::get('order/{id}', [TradingController::class, 'getOrder'])->name('order.show');
			Route::delete('order/{id}', [TradingController::class, 'cancelOrder'])->name('order.cancel');
		});

		// Notification routes
		Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
		Route::get('notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark');
		Route::get('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');
		Route::delete('notifications/delete/{id}', [NotificationController::class, 'destroy'])->name('notifications.delete');
		Route::delete('notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.deleteAll');
		
		// API routes for notifications
		Route::get('api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
		Route::get('api/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
	});
});
Route::post('sendcontact', 'App\Http\Controllers\User\UsersController@sendcontact')->name('enquiry');