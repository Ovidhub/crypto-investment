# Copy Trading Module - User Guide

## 📋 Table of Contents
1. [Overview](#overview)
2. [For Administrators](#for-administrators)
3. [For Users](#for-users)
4. [Features Overview](#features-overview)
5. [Frequently Asked Questions](#frequently-asked-questions)

---

## Overview

The **Copy Trading Module** allows users to automatically replicate the trades of expert traders. Users invest their capital, and when the expert trader distributes profits, all subscribers (copiers) receive proportional returns based on their investment amount.

### Key Benefits
- ✅ **Copy Expert Traders**: Follow and replicate successful traders' strategies
- ✅ **Passive Income**: Earn profits without actively trading
- ✅ **Multiple Risk Levels**: Choose from Low, Medium, or High-risk traders
- ✅ **Flexible Investment**: Set custom amounts and durations
- ✅ **Manual or Automatic**: Admin can distribute profits manually or set automatic intervals
- ✅ **Transparent Performance**: View traders' ROI, win rates, and active trades before subscribing

---

## For Administrators

### 1. Accessing Copy Trading Management

1. Log in to your admin panel
2. Look for **"Copy Trading"** in the left sidebar
3. Click to view all expert traders

### 2. Creating a New Expert Trader

**Step-by-step:**

1. Navigate to **Copy Trading → Create Trader**
2. Fill in **Basic Information**:
   - **Trader Name**: e.g., "John Smith"
   - **Profile Photo**: Upload trader's photo (400x400px recommended)
   - **Biography**: Brief description of trader's experience and strategy

3. Set **Investment Limits**:
   - **Minimum Capital**: Lowest investment amount (e.g., $100)
   - **Maximum Capital**: Highest investment amount (e.g., $10,000)

4. Configure **Follower Settings**:
   - **Maximum Followers**: Real subscriber limit (e.g., 50)
   - **Preset Followers**: Fake follower count for social proof
     - Example: 100 preset + 5 real = shows "105 followers" to users
   - **Copy Duration**: How long users can subscribe for
     - Options: 1-7 Days, 14 Days, 30 Days, 60 Days, 90 Days, 1-6 Months, 1 Year

5. Set **Performance Metrics**:
   - **Expected ROI (%)**: Return on investment percentage (e.g., 15%)
   - **Win Rate (%)**: Success rate (e.g., 70%)
   - **Active Trades**: Number of currently open trades (e.g., 5)

6. Choose **Risk Level**:
   - **Low**: Conservative trading, lower returns, minimal risk
   - **Medium**: Balanced approach, moderate returns
   - **High**: Aggressive trading, higher returns, significant risk

7. Set **Status**:
   - **Active**: Trader accepts new subscribers
   - **Inactive**: Hidden from users, cannot accept new subscribers

8. Configure **Automatic Profit Distribution** (Optional):
   - **Auto Distribute**: Toggle on/off
   - **Distribution Interval**: How often profits are distributed
     - Every 5 minutes
     - Every 15 minutes
     - Every 30 minutes
     - Every 1 hour
     - Every 6 hours
     - Every 12 hours
     - Daily
   - **Profit Percentage**: Fixed percentage distributed per interval
   - **Loss Percentage**: Loss percentage applied per interval (optional)
   - **Random Variation**: Enable for realistic profit/loss variations

9. Click **"Create Expert Trader"** to save

### 3. Managing Existing Expert Traders

#### Viewing All Traders
- Go to **Copy Trading → Trading Experts**
- See list with:
  - Trader name, photo, and bio
  - ROI, Win Rate, Risk Level
  - Number of followers (preset + real)
  - Status (Active/Inactive)
  - Action buttons (Edit, View Subscribers, Distribute Profit, Toggle Status, Delete)

#### Editing a Trader
1. Click **"Edit"** button on any trader
2. Update any field
3. Current values pre-fill the form
4. Save changes

#### Viewing Subscribers
1. Click **"Subscribers"** button on a trader
2. See all users copying this trader
3. View their:
   - User name and email
   - Amount invested
   - Current profit/loss
   - Subscription status (Active, Completed, Cancelled)
   - Start and end dates
   - Total return (investment + profit)

#### Toggling Trader Status
1. Click **"Toggle Status"** to quickly activate/deactivate
2. Inactive traders are hidden from users
3. Active subscriptions continue even if trader becomes inactive

#### Deleting a Trader
1. Click **"Delete"** button
2. Confirm deletion
3. **Important**: Cannot delete if trader has active subscribers
4. Cancel all subscriptions first before deleting

### 4. Distributing Profits Manually

**When to Use:**
- Manually distribute profits to all active subscribers at once
- Control exact profit/loss percentages
- One-time profit distribution events

**How to Distribute:**

1. Click **"Distribute Profit"** on a trader
2. You'll see:
   - Number of active subscribers
   - Total active investment amount
3. Enter **Profit Percentage**:
   - Positive number for profits (e.g., 5 = 5% profit)
   - Negative number for losses (e.g., -2 = 2% loss)
4. Preview appears showing:
   - Each subscriber's name
   - Investment amount
   - Calculated profit/loss
   - New total balance
5. Click **"Distribute Profit"** to confirm
6. **What Happens:**
   - All active subscribers receive profit/loss proportional to their investment
   - Subscriber's `current_profit` field updates
   - Transaction records created for each subscriber
   - Email notifications sent to all subscribers

**Example:**
- Trader has 3 active subscribers
- You enter 10% profit
- Subscriber A invested $500 → receives $50 profit
- Subscriber B invested $1,000 → receives $100 profit
- Subscriber C invested $2,500 → receives $250 profit

### 5. Automatic Profit Distribution

**How It Works:**
1. Enable "Auto Distribute" when creating/editing a trader
2. Set distribution interval (e.g., every 1 hour)
3. Set profit percentage (e.g., 2%)
4. Optionally set loss percentage (e.g., -0.5%)
5. Optionally enable random variation for realistic results

**Background Process:**
- Every 5 minutes, a background job runs (Laravel Scheduler)
- Checks all traders with auto_distribute enabled
- Checks if enough time has passed since last distribution
- Calculates profit/loss for each active subscriber
- Updates subscriber balances automatically
- Creates transaction records
- Sends notifications

**Important Settings:**
- Must enable in Admin → Settings → **Trade Mode: ON**
- Must enable **Copy Trading Auto Distribute: ON**
- Cron job must be running: `php artisan schedule:run` every minute

**Realistic Variations:**
- Enable "Random Variation" when creating trader
- System will randomly apply profit or loss on each interval
- Creates more realistic trading patterns
- Example: Instead of always 2% profit, might distribute 1.5%, 2.3%, or -0.3% randomly

### 6. Global Settings

**Location:** Admin → Settings

**Copy Trading Settings:**
- **Copy Trading Auto Distribute**: Master toggle for automatic distributions
- **Trade Mode**: Must be ON for copy trading to work
- These settings affect all traders with auto_distribute enabled

### 7. Monitoring Performance

**Dashboard Metrics:**
- Total expert traders
- Total active subscribers
- Total investment capital
- Average ROI across traders

**Per-Trader Analytics:**
- Total followers (preset + real)
- Current active subscribers
- Total capital invested
- Total profits distributed
- Win/loss distribution

---

## For Users

### 1. Browsing Expert Traders

**How to Access:**
1. Log in to your account
2. Navigate to **"Copy Trading"** in the main menu
3. You'll see the Expert Traders dashboard with:
   - **Stats Banner**:
     - Active Traders: Number of available traders
     - Avg ROI: Average return across all traders
     - Win Rate: Average success rate
     - Active Trades: Total ongoing trades

**Understanding Trader Cards:**
Each trader displays:
- **Profile Photo**: Visual identifier
- **Trader Name**: Expert's name
- **Top Badge**: Shown if win rate ≥ 75%
- **Followers**: Number of copiers (includes preset count)
- **Risk Level Badge**:
  - 🟢 Green = Low Risk
  - 🟡 Yellow = Medium Risk
  - 🔴 Red = High Risk
- **ROI**: Return on investment percentage
- **Win Rate**: Success percentage
- **Active Trades**: Currently open positions
- **Investment Range**: Min/Max capital required
- **Biography**: Trader's description and strategy

### 2. Searching and Filtering

**Search Bar:**
- Type trader name, risk level, or keywords
- Real-time filtering
- Shows matching traders instantly

**Filter Buttons:**
- **All Levels**: Show all traders
- **Low Risk**: Only conservative traders
- **Medium Risk**: Balanced traders
- **High Risk**: Aggressive traders

**Sorting:**
- Traders automatically sorted by ROI (highest first)
- Top performers appear at the top

### 3. Subscribing to a Trader

**Step-by-step:**

1. Click **"Copy Trader"** button on any trader card
2. A subscription form appears in a modal
3. Enter **Copy Amount**:
   - Type your investment in dollars
   - Must be between trader's min/max limits
   - Example: If trader requires $100-$10,000, enter within this range
4. Click **"Start Copying"**
5. **What Happens Next:**
   - Amount is deducted from your account balance
   - Subscription is activated immediately
   - You start receiving profits when admin distributes them
   - You receive a confirmation notification

**Before You Subscribe:**
- Check trader's **Risk Level** matches your comfort
- Review **ROI** and **Win Rate** for performance history
- Ensure you have sufficient account balance
- Understand the trader's **Copy Duration** (fixed per trader)

### 4. Managing Your Subscriptions

**Viewing Active Subscriptions:**
1. Go to **"My Copy Trading Subscriptions"** from the main menu
2. See all your subscriptions with:
   - Trader name and photo
   - Status badge (Active, Completed, Cancelled)
   - Risk level indicator
   - Subscription duration
   - Days remaining
   - Start date

**Subscription Card Details:**
- **Invested**: Original amount you invested
- **Current Profit**: Live profit/loss (updates after each distribution)
- **Total Return**: Invested + Current Profit
- **Profit Percentage**: ROI percentage
- **Profit History**: Chart showing profit growth over time

**Available Actions:**
- **View Trader**: Go back to trader's details
- **Cancel Subscription**: Stop copying and withdraw funds

### 5. Cancelling a Subscription

**How to Cancel:**
1. On **"My Copy Trading Subscriptions"** page
2. Find the subscription you want to cancel
3. Click the red **"Cancel Subscription"** button
4. Confirm the cancellation prompt
5. **What Happens:**
   - Subscription status changes to "Cancelled"
   - Your original investment + current profit is returned to your account balance
   - Trader stops distributing profits to you
   - You receive a notification with the refund amount

**Example:**
- You invested: $1,000
- Current profit: $150
- Total refunded: $1,150

**Important Notes:**
- You can cancel anytime, even before duration ends
- No cancellation fees
- Refund is instant
- Cannot reactivate a cancelled subscription (must subscribe again)

### 6. Understanding Profit Updates

**How Profits Are Added:**
- Admin distributes profits manually OR automatically
- You receive a percentage based on your investment
- Example: Admin distributes 5% profit
  - You invested $1,000
  - You receive $50 profit
  - New current profit: $50
  - Total return: $1,050

**Losses:**
- Some distributions may be negative (losses)
- Example: Admin distributes -2% loss
  - You invested $1,000
  - You lose $20
  - New current profit: -$20
  - Total return: $980

**Notifications:**
- You receive an email for each profit/loss distribution
- Shows amount added/deducted
- Updates your current profit balance

### 7. Best Practices for Users

**Choosing the Right Trader:**
1. **Review Performance**: Higher ROI and Win Rate = better
2. **Check Risk Level**: Match with your risk tolerance
3. **Read Biography**: Understand trader's strategy
4. **Check Followers**: More followers often indicates trust
5. **Diversify**: Consider copying multiple traders with different risk levels

**Investment Tips:**
- Start with minimum capital to test
- Don't invest more than you can afford to lose
- High Risk = High Reward but also High Loss potential
- Monitor your subscriptions regularly
- Cancel if trader's performance drops significantly

**Risk Management:**
- **Low Risk**: Suitable for conservative investors, steady but smaller returns
- **Medium Risk**: Balanced approach, moderate returns
- **High Risk**: For aggressive investors willing to accept volatility

---

## Features Overview

### Admin Features
✅ **Unlimited Traders**: Create as many expert traders as needed  
✅ **Preset Followers**: Add fake follower counts for social proof  
✅ **Manual Profit Distribution**: Distribute profits to all subscribers at once  
✅ **Automatic Distribution**: Set intervals and percentages for automated distributions  
✅ **Random Variations**: Enable realistic profit/loss patterns  
✅ **Subscriber Management**: View all copiers for each trader  
✅ **Performance Tracking**: Monitor ROI, followers, and profits  
✅ **Risk Level Classification**: Low, Medium, High categories  
✅ **Flexible Durations**: 1 day to 1 year subscription options  
✅ **Status Toggle**: Quickly activate/deactivate traders  
✅ **Transaction Logging**: All distributions recorded in database  
✅ **Email Notifications**: Automatic emails for subscriptions and distributions  

### User Features
✅ **Easy Trader Discovery**: Browse traders with search and filters  
✅ **Risk-Based Filtering**: Filter by Low, Medium, High risk  
✅ **Transparent Metrics**: See ROI, win rate, followers before investing  
✅ **One-Click Subscription**: Simple modal-based subscription process  
✅ **Live Profit Tracking**: Watch earnings update in real-time  
✅ **Cancel Anytime**: Get investment + profit back instantly  
✅ **Profit History**: Visual charts showing profit growth  
✅ **Multiple Subscriptions**: Copy multiple traders simultaneously  
✅ **Mobile Responsive**: Use on any device  
✅ **Notifications**: Get alerts for subscriptions and profit distributions  
✅ **Status Indicators**: Clear badges for Active/Completed/Cancelled  

### Technical Features
✅ **Manual Distribution**: Admin-triggered profit/loss distribution  
✅ **Automated Distribution**: Laravel Scheduler runs every 5 minutes  
✅ **Interval-Based**: Configure distribution frequency per trader  
✅ **Proportional Calculations**: Profits calculated based on investment amount  
✅ **Balance Management**: Automatic debit/credit of user accounts  
✅ **Status Tracking**: Active, Completed, and Cancelled subscription states  
✅ **Transaction Records**: All distributions logged in tp_transactions table  
✅ **Clean Modern UI**: Professional design with Tailwind CSS  
✅ **Dark Mode**: Full dark/light theme support  
✅ **Capacity Management**: Traders have maximum follower limits  

---

## Frequently Asked Questions

### For Administrators

**Q: What's the difference between manual and automatic profit distribution?**  
A:
- **Manual**: You click a button and distribute profits whenever you want. Full control over timing and amounts.
- **Automatic**: System distributes profits automatically based on interval (e.g., every hour) and preset percentages. Runs in the background.

**Q: How does the preset followers feature work?**  
A: When creating a trader, you can set "Preset Followers" to any number (e.g., 100). If the trader has 5 real subscribers, the system shows "105 followers" to users. This creates social proof to attract more subscribers.

**Q: Can I distribute losses to subscribers?**  
A: Yes! Enter a negative percentage (e.g., -3 for 3% loss) when distributing. Subscribers' current_profit decreases, and their total return reduces accordingly.

**Q: What happens if I delete a trader with active subscribers?**  
A: The system prevents deletion. You must first cancel all active subscriptions or wait for them to complete/expire before deleting the trader.

**Q: How do I enable automatic distribution?**  
A:
1. Go to Admin → Settings → enable "Trade Mode" and "Copy Trading Auto Distribute"
2. When creating/editing a trader, toggle "Auto Distribute" to ON
3. Set distribution interval and profit/loss percentages
4. Ensure cron job is running: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

**Q: What's "Random Variation" in automatic distribution?**  
A: When enabled, the system randomly decides to distribute profit or loss on each interval, creating realistic trading patterns. Instead of always +2%, it might give +2.3%, +1.7%, -0.5%, etc.

**Q: Can I change a trader's settings after users have subscribed?**  
A: Yes, but:
- Changes to ROI, Win Rate, Risk Level are cosmetic (don't affect active subscriptions)
- Changes to profit distribution settings take effect immediately for future distributions
- Cannot change duration for existing subscriptions (only affects new subscribers)

---

### For Users

**Q: How do I know which trader to choose?**  
A: Consider these factors:
- **High ROI + High Win Rate** = Best performers
- **Low Risk** = Safer, smaller returns
- **High Risk** = Bigger returns but also bigger losses
- **More Followers** = Popular and trusted
- **Active Trades** = Actively trading vs sitting idle

**Q: When will I receive profits?**  
A: Depends on the trader's distribution method:
- **Manual**: When admin manually distributes (no fixed schedule)
- **Automatic**: Based on trader's interval (e.g., every hour, daily, etc.)
You receive an email notification each time profits are distributed.

**Q: Can I lose money with copy trading?**  
A: Yes. If the trader makes losing trades, admin may distribute losses. Your current_profit can go negative. However, you can cancel anytime to prevent further losses.

**Q: What if my current profit is negative?**  
A: You still get your original investment back plus/minus the current profit when cancelling.
- Example: Invested $1,000, current profit is -$50
- If you cancel: You get back $950 ($1,000 - $50)

**Q: Can I copy multiple traders at once?**  
A: Yes! You can subscribe to as many traders as you want, each with different amounts. Diversification reduces risk.

**Q: What happens when my subscription duration ends?**  
A: The subscription automatically changes to "Completed" status, and your investment + current profit is returned to your account balance. You receive a notification with the refund amount.

**Q: Why can't I subscribe to a trader?**  
A: Possible reasons:
- Insufficient account balance
- Trader reached maximum capacity (max followers limit)
- Your amount is below trader's minimum or above maximum
- Trader status is inactive

**Q: Do I get charged any fees for copying traders?**  
A: That depends on how the admin configured the platform. Typically, the admin may take a percentage of profits (configured separately), but there are no cancellation fees or subscription fees beyond your investment amount.

**Q: Can I withdraw my profits without cancelling?**  
A: No. Profits are locked in the subscription until you cancel or the duration ends. This allows compound growth as the admin calculates profits on your total balance (investment + current profit).

---

## System Requirements

**Server:**
- PHP 7.3 or higher
- MySQL 5.7 or higher
- Laravel 8.x
- Cron job access for automatic distributions

**Cron Job Setup (Linux):**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Cron Job Setup (Windows Task Scheduler):**
- Action: Start a program
- Program: `php`
- Arguments: `artisan schedule:run`
- Start in: `c:\xampp\htdocs\brembypro`
- Trigger: Every 1 minute or every 5 minutes

**Browser (Users):**
- Modern browser (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Responsive design works on mobile/tablet

---

## Database Tables

The Copy Trading module uses these database tables:

**expert_traders:**
- Stores trader profiles
- Fields: name, photo, bio, min_capital, max_capital, max_followers, preset_followers, roi_percentage, win_rate, risk_level, auto_distribute, distribution_interval, etc.

**expert_trader_subscribers:**
- Stores user subscriptions
- Fields: user_id, expert_trader_id, amount_invested, current_profit, start_date, end_date, status

**tp_transactions:**
- Logs all transactions
- Records each profit/loss distribution
- Type: "Copy Trading Subscription", "Copy Trading Profit", "Copy Trading Return"

---

## Troubleshooting

**Automatic distributions not working:**
1. Check Admin → Settings → "Trade Mode" is ON
2. Check "Copy Trading Auto Distribute" is ON
3. Verify cron job is running: `php artisan schedule:run`
4. Check trader has "auto_distribute" enabled
5. Check trader has active subscribers
6. Check logs: `storage/logs/laravel.log`

**Subscribers not receiving profits:**
1. Check subscription status is "Active"
2. Check end_date hasn't passed
3. Check user's current_profit field updates in database
4. Check transaction records in tp_transactions table
5. Check email notifications are configured

**Trader shows wrong follower count:**
1. Remember displayed count = preset_followers + total_followers (real count)
2. Check trader's preset_followers value
3. Check active subscribers count
4. Cancelled/completed subscriptions decrement total_followers

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
   - Database transactions

---

## Credits

**Built with:**
- Laravel 8.x Framework
- Tailwind CSS for modern UI
- Lucide Icons
- Chart.js for profit history graphs

**Version:** 1.0  
**Last Updated:** January 2026
