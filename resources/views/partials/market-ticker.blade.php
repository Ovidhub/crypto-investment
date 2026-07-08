{{-- Live scrolling market price ticker (CoinGecko, client-side, no API key). Reusable on any page. --}}
<div id="mkt-ticker" class="mkt-ticker" aria-label="Live crypto prices">
    <div class="mkt-track" id="mkt-track">
        <div class="mkt-loading">Loading live market prices…</div>
    </div>
</div>

<style>
    .mkt-ticker{position:relative;width:100%;overflow:hidden;background:#0b0e15;border-top:1px solid #1e2530;border-bottom:1px solid #1e2530;
        white-space:nowrap;-webkit-mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent);
        mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent)}
    .mkt-track{display:inline-flex;align-items:center;gap:0;will-change:transform;animation:mkt-scroll 60s linear infinite}
    .mkt-ticker:hover .mkt-track{animation-play-state:paused}
    @keyframes mkt-scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
    .mkt-item{display:inline-flex;align-items:center;gap:8px;padding:11px 20px;border-right:1px solid #1e2530;font-family:"DM Sans",system-ui,sans-serif;font-size:13px}
    .mkt-item img{width:18px;height:18px;border-radius:50%}
    .mkt-sym{color:#e7ebf2;font-weight:700;text-transform:uppercase}
    .mkt-price{color:#c7cedb;font-weight:500}
    .mkt-chg{font-weight:600;font-size:12px}
    .mkt-up{color:#22e58a}
    .mkt-down{color:#ff6b6b}
    .mkt-loading{padding:11px 20px;color:#9aa3b2;font-size:13px;font-family:"DM Sans",system-ui,sans-serif}
</style>

<script>
(function(){
    var track=document.getElementById('mkt-track'); if(!track) return;
    var IDS='bitcoin,ethereum,tether,binancecoin,solana,ripple,cardano,dogecoin,tron,chainlink,avalanche-2,litecoin,polkadot,matic-network';
    var URL='https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids='+IDS+'&order=market_cap_desc&price_change_percentage=24h';

    function fmt(n){
        if(n===null||n===undefined) return '—';
        if(n>=1) return n.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
        return n.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:6});
    }
    function itemHTML(c){
        var chg=(c.price_change_percentage_24h===null||c.price_change_percentage_24h===undefined)?0:c.price_change_percentage_24h;
        var up=chg>=0;
        return '<div class="mkt-item">'+
            '<img src="'+c.image+'" alt="" loading="lazy">'+
            '<span class="mkt-sym">'+c.symbol+'</span>'+
            '<span class="mkt-price">$'+fmt(c.current_price)+'</span>'+
            '<span class="mkt-chg '+(up?'mkt-up':'mkt-down')+'">'+(up?'▲':'▼')+' '+Math.abs(chg).toFixed(2)+'%</span>'+
        '</div>';
    }
    function render(list){
        var html=list.map(itemHTML).join('');
        // duplicate the row so the marquee loops seamlessly (translateX -50%)
        track.innerHTML=html+html;
    }
    function load(){
        fetch(URL).then(function(r){return r.json();}).then(function(data){
            if(Array.isArray(data)&&data.length){ render(data); }
        }).catch(function(){});
    }
    load();
    setInterval(load, 60000);
})();
</script>
