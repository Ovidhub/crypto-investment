# Bot Trading Module - User Guide

## 📋 Table of Contents
1. [Overview](#overview)
2. [For Administrators](#for-administrators)
3. [For Users](#for-users)
4. [Features Overview](#features-overview)
5. [Frequently Asked Questions](#frequently-asked-questions)

---

## Overview

The **Bot Trading Module** is an automated cryptocurrency trading system that allows users to invest their funds with AI-powered trading bots. The bots execute trades automatically based on predefined strategies, generating profits for subscribers.

### Key Benefits
- ✅ **Automated Trading**: Bots trade 24/7 without human intervention
- ✅ **Multiple Strategies**: Choose from Scalping, Day Trading, or Swing Trading bots
- ✅ **Real-Time Performance**: Track live profit/loss and trade history
- ✅ **Flexible Investment**: Set custom investment amounts and duration
- ✅ **Risk Management**: Cancel subscriptions anytime and withdraw funds

---

## For Administrators

### 1. Accessing Bot Trading Management

1. Log in to your admin panel
2. Look for **"Bot Trading"** in the left sidebar (icon: robot)
3. Click to expand the submenu:
   - **Trading Bots** - View all bots
   - **Create Bot** - Add new trading bot

### 2. Creating a New Trading Bot

**Step-by-step:**

1. Navigate to **Bot Trading → Create Bot**
2. Fill in the **Basic Information**:
   - **Bot Name**: e.g., "Quantum Trader AI"
   - **Description**: Explain the bot's strategy and features
   - **Strategy Type**: Choose from:
     - **Scalping**: Quick trades (seconds to minutes)
     - **Day Trading**: Trades within a day
     - **Swing Trading**: Multi-day trades
   - **Bot Photo**: Upload a 400x400px image (optional)

3. Set **Performance Metrics**:
   - **Win Rate (%)**: Expected success rate (60-90% recommended)
   - **ROI (%)**: Return on investment percentage
   - **Status**: Active or Inactive

4. Configure **Investment Limits**:
   - **Minimum Capital**: Lowest investment amount (e.g., $100)
   - **Maximum Capital**: Highest investment amount (e.g., $10,000)

5. Set **Automation Settings**:
   - **Auto Trading**: On or Off
   - **Trade Interval**: How often bot trades (e.g., "Every 5 minutes")
   - **Min Profit**: Minimum profit per trade (e.g., 1%)
   - **Max Profit**: Maximum profit per trade (e.g., 5%)
   - **Min Loss**: Minimum loss per trade (e.g., -0.5%)
   - **Max Loss**: Maximum loss per trade (e.g., -2%)

6. **Boost Active Users Count** (Optional):
   - Add a number to display alongside real subscribers
   - Example: 20 fake + 3 real = shows "23 active users" to customers
   - This creates social proof to attract more subscribers

7. Click **"Create Bot"** to save

### 3. Managing Existing Bots

#### Viewing All Bots
- Go to **Bot Trading → Trading Bots**
- See list of all bots with:
  - Bot name, strategy, and status
  - Performance stats (ROI, Win Rate, Total Profit)
  - Number of active subscribers
  - Action buttons (Edit, View Subscribers, Delete)

#### Editing a Bot
1. Click **"Edit"** button on any bot
2. Update any field (same as creation form)
3. The form shows current values
4. Save changes

#### Managing Bot Subscribers
1. Click **"Subscribers"** button on a bot
2. View all users subscribed to this bot
3. See their:
   - Investment amount
   - Current profit/loss
   - Subscription status (Active, Completed, Cancelled)
   - Start and end dates

#### Viewing/Managing Trades
1. Click **"Trades"** on a specific subscriber
2. See all trades executed for that subscription
3. **Create Manual Trade**:
   - Click **"Create Trade"** button
   - Select cryptocurrency from dropdown (shows logo preview)
   - Enter entry and exit prices
   - Set trade amount (units)
   - Choose outcome: Win or Loss
   - Optionally set opened/closed timestamps
   - Add admin notes for reference
   - Click **"Create Trade"**

4. **Edit Existing Trade**:
   - Click **"Edit"** on any trade
   - Modify prices, amounts, or outcomes
   - Save changes

5. **Delete Trade**:
   - Click **"Delete"** on any trade
   - Confirm deletion

### 4. Understanding the Automated System

**How It Works:**
1. Every **5 minutes**, a background job runs automatically
2. The job checks all active bot subscriptions
3. For each subscription:
   - Randomly selects a cryptocurrency from your database
   - Randomly decides to trade or skip (70% chance to trade)
   - Calculates profit/loss based on bot's settings
   - Creates a trade record with win/loss outcome
   - Updates subscriber's profit balance
   - Sends notification to user

**Important:** The system uses your `crypto_prices` table which contains 62 cryptocurrencies with:
- Bitcoin (BTC), Ethereum (ETH), and 60+ other coins
- Live logo images from CoinGecko
- Current prices in USD
- Market cap data

### 5. Monitoring Performance

**Dashboard Metrics:**
- Total active bots
- Total subscribers
- Total revenue from investments
- Average ROI across all bots

**Per-Bot Analytics:**
- Total trades executed
- Win/loss ratio
- Total profit generated
- Active subscribers count (real + boosted)

---

## For Users

### 1. Browsing Available Trading Bots

**How to Access:**
1. Log in to your account
2. Navigate to **"Bot Trading"** in the main menu
3. You'll see the Bot Trading dashboard with:
   - **Total Bots**: Number of available bots
   - **Active Users**: Current subscribers across all bots
   - **Success Rate**: Average win rate
   - Filter buttons by strategy type

**Understanding Bot Cards:**
Each bot displays:
- **Bot Name & Icon**: Visual identifier
- **Live Badge**: Green badge if auto-trading is active
- **Strategy Type**: Scalping, Day Trading, or Swing Trading
- **Active Users**: Number of current subscribers
- **Trade Interval**: How often it trades
- **ROI**: Return on investment percentage
- **Win Rate**: Success percentage
- **Total Trades**: Historical trade count
- **Investment Range**: Min/Max capital required
- **Total Profit**: Bot's cumulative earnings
- **Subscribe Button**: Click to view details

### 2. Viewing Bot Details

**What You'll See:**
1. Click **"Subscribe Now"** on any bot
2. Bot Detail Page shows:
   - **Performance Metrics**:
     - ROI percentage
     - Win rate
     - Total trades
     - Total profit
   
   - **About This Bot**:
     - Detailed strategy description
     - Minimum and maximum investment amounts
   
   - **Recent Trades** (last 5):
     - Cryptocurrency traded (with logo)
     - Time of trade
     - Profit/loss amount
     - Profit percentage

### 3. Subscribing to a Bot

**Step-by-step:**
1. On the bot detail page, find the **"Subscribe to Bot"** form (right side)
2. Enter **Investment Amount**:
   - Type your desired investment in dollars
   - Must be between the bot's min/max limits
   - Example: If bot requires $100-$10,000, enter between this range
   
3. Select **Duration**:
   - Choose from dropdown:
     - 1 Day (for quick testing)
     - 2, 3, 4, 5, 6, 7 Days (short-term)
     - 30 Days (1 month)
     - 60 Days (2 months)
     - 90 Days (3 months)
   
4. Read the **Important Notice**:
   - Your funds will be automatically traded
   - You can cancel anytime
   - Invested amount + current profit will be returned if cancelled
   
5. Click **"Subscribe Now"**
   
6. **What Happens Next:**
   - Amount is deducted from your account balance
   - Subscription is activated immediately
   - Bot starts trading within the next 5-minute interval
   - You receive a notification confirming subscription

### 4. Managing Your Subscriptions

**Viewing Active Subscriptions:**
1. Go to **"My Bot Subscriptions"** from the main menu
2. See all your subscriptions with:
   - Bot name and icon
   - Status badge (Active, Completed, Cancelled)
   - Subscription duration
   - Days remaining
   - Start date

**Subscription Card Details:**
- **Invested**: Original amount you invested
- **Current Profit**: Live profit/loss (updates after each trade)
- **Total Return**: Invested + Current Profit
- **Profit Percentage**: ROI percentage
- **Last Trade**: When the bot last executed a trade

**Available Actions:**
- **View Bot**: Go back to bot details page
- **Cancel**: Stop subscription and withdraw funds

### 5. Cancelling a Subscription

**How to Cancel:**
1. On **"My Bot Subscriptions"** page
2. Find the subscription you want to cancel
3. Click the red **"Cancel"** button
4. Confirm the cancellation prompt
5. **What Happens:**
   - Subscription status changes to "Cancelled"
   - Your original investment + current profit is returned to your account balance
   - Bot stops trading for this subscription
   - You receive a notification with the refund amount

**Example:**
- You invested: $500
- Current profit: $35
- Total refunded: $535

### 6. Viewing Trade History

**Accessing Your Trades:**
1. Navigate to **"Bot Trades"** from the menu
2. See **Statistics Banner**:
   - **Total Trades**: Number of executed trades
   - **Wins**: Successful trades
   - **Net Profit**: Total earnings across all bots

**Trade List Shows:**
- Cryptocurrency logo and symbol
- Bot that executed the trade
- Trade outcome (Win or Loss)
- Profit/loss amount
- Percentage change
- Date and time

**Filtering:**
- View trades from all your bot subscriptions
- Paginated for easy browsing (20 trades per page)

---

## Features Overview

### Admin Features
✅ **Unlimited Bots**: Create as many trading bots as needed  
✅ **Custom Strategies**: Three strategy types with custom settings  
✅ **Manual Trade Creation**: Add trades manually for any subscriber  
✅ **Trade Editing**: Modify existing trades (prices, outcomes, amounts)  
✅ **Subscriber Management**: View all users subscribed to each bot  
✅ **Social Proof Boosting**: Add fake active user counts to attract customers  
✅ **Performance Tracking**: Monitor ROI, win rates, and profitability  
✅ **Automated Trading**: Background job runs every 5 minutes  
✅ **62 Cryptocurrencies**: Pre-loaded with major crypto assets and logos  

### User Features
✅ **Easy Bot Discovery**: Browse bots with search and filters  
✅ **Transparent Metrics**: See real performance data before investing  
✅ **Flexible Investments**: Choose amount and duration (1-90 days)  
✅ **Live Profit Tracking**: Watch your earnings update in real-time  
✅ **Cancel Anytime**: Get your investment + profit back instantly  
✅ **Trade History**: View all executed trades with full details  
✅ **Multiple Subscriptions**: Invest in multiple bots simultaneously  
✅ **Mobile Responsive**: Use on any device  
✅ **Notifications**: Get alerts for subscriptions and trades  

### Technical Features
✅ **Automated Execution**: Laravel Scheduler runs trades every 5 minutes  
✅ **Real Crypto Data**: Uses crypto_prices table with CoinGecko logos  
✅ **Smart Profit Calculation**: Based on admin-defined min/max profit/loss  
✅ **Transaction Logging**: All trades recorded in database  
✅ **Balance Management**: Automatic debit/credit of user accounts  
✅ **Status Tracking**: Active, Completed, and Cancelled subscription states  
✅ **Clean Modern UI**: Professional design with Tailwind CSS  
✅ **Dark Mode**: Full dark/light theme support  

---

## Frequently Asked Questions

### For Administrators

**Q: How do I make the automated trading start working?**  
A: The automated trading runs via Laravel Scheduler. You need to add this to your server's cron job or Windows Task Scheduler:
```
php artisan schedule:run
```
Run this command every 5 minutes. The system will automatically execute trades for all active subscriptions.

**Q: What happens if I set a very high win rate (e.g., 95%)?**  
A: The bot will win approximately 95% of trades. However, this may look unrealistic to users. We recommend 65-85% for credibility.

**Q: Can I manually create trades for testing?**  
A: Yes! Go to any subscriber's trades page and click "Create Trade". You can manually set all parameters including outcome (win/loss).

**Q: What's the purpose of "Boost Active Users Count"?**  
A: It adds fake user numbers to the real subscriber count. If you set 50 and have 5 real users, it shows "55 active" to customers, creating social proof.

**Q: Can I edit a bot's profit settings after users have subscribed?**  
A: Yes, changes take effect immediately for new trades. Existing trades remain unchanged.

**Q: How do I add more cryptocurrencies?**  
A: Add records to the `crypto_prices` table with these fields:
- `coin_symbol` (e.g., "BTC")
- `name` (e.g., "Bitcoin")
- `logo` (CoinGecko image URL)
- `price_usd` (current price)

---

### For Users

**Q: Is my money safe?**  
A: Your investment is tracked in the database and can be withdrawn at any time by cancelling your subscription. The admin has full control over the system.

**Q: How often does the bot trade?**  
A: The bot checks for trading opportunities every 5 minutes. It may or may not trade on each interval based on the bot's settings.

**Q: Can I subscribe to multiple bots at once?**  
A: Yes! You can subscribe to as many bots as you want, each with different investment amounts and durations.

**Q: What happens when my subscription ends?**  
A: The subscription automatically changes to "Completed" status, and your investment + profit is returned to your account balance.

**Q: Can I cancel before the duration ends?**  
A: Yes, absolutely! Click "Cancel" on any active subscription and you'll immediately receive your invested amount plus any current profit.

**Q: Do I lose money if a trade is a loss?**  
A: Yes, individual trades can lose money. However, the bot's win rate determines overall profitability. A bot with 75% win rate will win 3 out of 4 trades on average.

**Q: How do I know which bot to choose?**  
A: Look at:
- **ROI**: Higher percentage means better returns
- **Win Rate**: Higher win rate = more consistent profits
- **Total Trades**: More trades = proven track record
- **Active Users**: More users = popular and trusted

**Q: Can I withdraw my profit without cancelling?**  
A: No, profit is locked in the subscription until you cancel or the duration ends. This is to allow the bot to reinvest profits for compound growth.

---

## Support

If you encounter any issues or have questions:

1. **Technical Issues**: Contact your system administrator
2. **User Support**: Refer users to this guide
3. **Customization**: Hire a Laravel developer familiar with:
   - Laravel 8.x framework
   - Eloquent ORM
   - Laravel Scheduler (Cron Jobs)
   - Blade templating

---

## System Requirements

**Server:**
- PHP 7.3 or higher
- MySQL 5.7 or higher
- Laravel 8.x
- Cron job access OR Windows Task Scheduler

**Browser (Users):**
- Modern browser (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Responsive design works on mobile/tablet

---

## Credits

**Built with:**
- Laravel 8.x Framework
- Tailwind CSS for modern UI
- Lucide Icons
- CoinGecko API for crypto logos

**Version:** 1.0  
**Last Updated:** January 2026

---

**🎉 Congratulations!** You now have a complete guide to the Bot Trading Module. Happy trading!
