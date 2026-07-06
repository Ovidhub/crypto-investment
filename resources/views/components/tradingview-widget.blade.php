@props(['symbol', 'height' => 150])

<div class="tradingview-widget-container">
    <div class="tradingview-widget-container__widget" id="tradingview_{{ md5($symbol) }}"></div>
    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
    {
      "symbol": "{{ $symbol }}",
      "width": "100%",
      "height": {{ $height }},
      "locale": "en",
      "dateRange": "12M",
      "colorTheme": "light",
      "isTransparent": false,
      "autosize": false,
      "largeChartUrl": "",
      "noTimeScale": true
    }
    </script>
</div>

<style>
    .tradingview-widget-container {
        border-radius: 0.75rem;
        overflow: hidden;
    }
</style>

<script>
// Update TradingView theme based on dark mode
document.addEventListener('DOMContentLoaded', function() {
    function updateTradingViewTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        // TradingView widgets will reload automatically when the page theme changes
    }
    
    // Watch for theme changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                updateTradingViewTheme();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true
    });
});
</script>
