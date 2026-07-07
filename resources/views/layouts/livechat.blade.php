{{-- WhatsApp button (kept) --}}
@if($settings->whatsapp)
<script type="text/javascript">
    (function () {
        var options = {
            whatsapp: "{{$settings->whatsapp}}",
            call_to_action: "Message us",
            position: "left",
            pre_filled_message: "Hello I am",
        };
        var proto = document.location.protocol, host = "getbutton.io", url = proto + "//static." + host;
        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
        s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
        var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
    })();
</script>
@endif

{{-- ============================================================
     Custom live chat widget (visitor side). Polling based.
     Hidden inside the admin area (admins use the inbox instead).
     ============================================================ --}}
@unless(request()->is('admin*'))
<div id="lc-root" data-csrf="{{ csrf_token() }}"
     data-msg-url="{{ url('/chat/message') }}"
     data-poll-url="{{ url('/chat/poll') }}"
     data-status-url="{{ url('/chat/status') }}"
     data-site="{{ $settings->site_name }}">
</div>

<style>
    #lc-launcher{position:fixed;right:20px;bottom:20px;z-index:2147483000;width:60px;height:60px;border:none;border-radius:50%;
        background:linear-gradient(135deg,#b57bff,#7c3aed);color:#fff;cursor:pointer;box-shadow:0 12px 30px -8px rgba(124,58,237,.6);
        display:flex;align-items:center;justify-content:center;transition:transform .2s ease}
    #lc-launcher:hover{transform:scale(1.06)}
    #lc-launcher svg{width:28px;height:28px}
    #lc-badge{position:absolute;top:-4px;right:-4px;min-width:20px;height:20px;padding:0 5px;border-radius:10px;background:#22e58a;
        color:#05231a;font:700 11px/20px system-ui,sans-serif;text-align:center;display:none}
    #lc-panel{position:fixed;right:20px;bottom:92px;z-index:2147483000;width:350px;max-width:calc(100vw - 32px);height:480px;max-height:calc(100vh - 120px);
        background:#0b0e15;border:1px solid #1e2530;border-radius:18px;overflow:hidden;display:none;flex-direction:column;
        box-shadow:0 30px 70px -20px rgba(0,0,0,.7);font-family:"DM Sans",system-ui,-apple-system,sans-serif}
    #lc-panel.lc-open{display:flex;animation:lc-in .22s ease}
    @keyframes lc-in{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    .lc-head{background:linear-gradient(135deg,#7c3aed,#b57bff);padding:16px;color:#fff}
    .lc-head h4{margin:0;font-size:16px;font-weight:700}
    .lc-status{display:flex;align-items:center;gap:6px;font-size:12px;opacity:.95;margin-top:3px}
    .lc-dot{width:8px;height:8px;border-radius:50%;background:#9aa3b2}
    .lc-dot.on{background:#22e58a;box-shadow:0 0 0 3px rgba(34,229,138,.25)}
    .lc-close{position:absolute;top:12px;right:12px;background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;
        border-radius:8px;cursor:pointer;font-size:16px;line-height:1}
    .lc-body{flex:1;overflow-y:auto;padding:14px;background:#0b0e15;display:flex;flex-direction:column;gap:8px}
    .lc-msg{max-width:80%;padding:9px 12px;border-radius:14px;font-size:14px;line-height:1.4;word-wrap:break-word;white-space:pre-wrap}
    .lc-msg.v{align-self:flex-end;background:linear-gradient(135deg,#b57bff,#7c3aed);color:#fff;border-bottom-right-radius:4px}
    .lc-msg.a{align-self:flex-start;background:#1a2130;color:#e7ebf2;border-bottom-left-radius:4px}
    .lc-time{font-size:10px;opacity:.6;margin-top:2px;display:block}
    .lc-intro{color:#9aa3b2;font-size:13px;text-align:center;padding:10px 6px}
    .lc-foot{border-top:1px solid #1e2530;padding:10px;background:#10141d}
    .lc-pre input,.lc-input textarea{width:100%;box-sizing:border-box;background:#0b0e15;border:1px solid #1e2530;border-radius:10px;
        color:#e7ebf2;padding:9px 11px;font-size:14px;font-family:inherit;outline:none;resize:none}
    .lc-pre input{margin-bottom:8px}
    .lc-input{display:flex;gap:8px;align-items:flex-end}
    .lc-input textarea{max-height:90px}
    .lc-send{flex:0 0 auto;background:linear-gradient(135deg,#b57bff,#7c3aed);border:none;color:#fff;width:40px;height:40px;
        border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center}
    .lc-send:disabled{opacity:.5;cursor:not-allowed}
    .lc-btn{width:100%;background:linear-gradient(135deg,#b57bff,#7c3aed);border:none;color:#fff;padding:10px;border-radius:10px;
        cursor:pointer;font-weight:700;font-size:14px;font-family:inherit}
    .lc-note{color:#9aa3b2;font-size:11px;text-align:center;margin-top:8px}
</style>

<script>
(function(){
    var root=document.getElementById('lc-root'); if(!root) return;
    var CSRF=root.dataset.csrf, MSG=root.dataset.msgUrl, POLL=root.dataset.pollUrl, STATUS=root.dataset.statusUrl, SITE=root.dataset.site||'Support';
    var LS='lc_state_v1';
    var state=JSON.parse(localStorage.getItem(LS)||'{}'); // {token,name,email,lastId}
    var open=false, pollTimer=null;

    // ---- build UI ----
    var launcher=document.createElement('button'); launcher.id='lc-launcher'; launcher.setAttribute('aria-label','Live chat');
    launcher.innerHTML='<svg viewBox="0 0 24 24" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span id="lc-badge">0</span>';
    document.body.appendChild(launcher);

    var panel=document.createElement('div'); panel.id='lc-panel';
    panel.innerHTML=
        '<div class="lc-head" style="position:relative">'+
            '<button class="lc-close" aria-label="Close">&times;</button>'+
            '<h4>Chat with '+esc(SITE)+'</h4>'+
            '<div class="lc-status"><span class="lc-dot" id="lc-dot"></span><span id="lc-status-text">Connecting…</span></div>'+
        '</div>'+
        '<div class="lc-body" id="lc-body"></div>'+
        '<div class="lc-foot" id="lc-foot"></div>';
    document.body.appendChild(panel);

    var body=panel.querySelector('#lc-body'), foot=panel.querySelector('#lc-foot');
    var dot=panel.querySelector('#lc-dot'), statusText=panel.querySelector('#lc-status-text'), badge=launcher.querySelector('#lc-badge');

    launcher.addEventListener('click',function(){ open?closePanel():openPanel(); });
    panel.querySelector('.lc-close').addEventListener('click',closePanel);

    function save(){ localStorage.setItem(LS, JSON.stringify(state)); }
    function esc(s){ return (s||'').replace(/[&<>"]/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}); }

    function renderFoot(){
        if(!state.token){
            foot.innerHTML='<div class="lc-pre">'+
                '<input id="lc-name" placeholder="Your name" value="'+esc(state.name||'')+'">'+
                '<input id="lc-email" type="email" placeholder="Email (optional)" value="'+esc(state.email||'')+'">'+
                '<textarea id="lc-first" rows="2" placeholder="How can we help?"></textarea>'+
                '<button class="lc-btn" id="lc-start" style="margin-top:8px">Start chat</button>'+
                '<div class="lc-note">We usually reply in a few minutes.</div>'+
            '</div>';
            foot.querySelector('#lc-start').addEventListener('click',startChat);
        } else {
            foot.innerHTML='<div class="lc-input">'+
                '<textarea id="lc-text" rows="1" placeholder="Type a message…"></textarea>'+
                '<button class="lc-send" id="lc-send" aria-label="Send"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>'+
            '</div>';
            var ta=foot.querySelector('#lc-text');
            foot.querySelector('#lc-send').addEventListener('click',sendMsg);
            ta.addEventListener('keydown',function(e){ if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMsg();} });
        }
    }

    function openPanel(){
        open=true; panel.classList.add('lc-open'); badge.style.display='none';
        if(body.childElementCount===0 && !state.token){
            body.innerHTML='<div class="lc-intro">👋 Hi! Send us a message and we\'ll help you out.</div>';
        }
        renderFoot();
        pollNow(); startPolling();
        if(state.token){ var t=foot.querySelector('#lc-text'); if(t) t.focus(); }
    }
    function closePanel(){ open=false; panel.classList.remove('lc-open'); stopPolling(); }

    function addMsg(m){
        if(m.id && state.lastId && m.id<=state.lastId) return;
        var d=document.createElement('div'); d.className='lc-msg '+(m.sender==='visitor'?'v':'a');
        d.innerHTML=esc(m.body)+'<span class="lc-time">'+esc(m.time||'')+'</span>';
        body.appendChild(d); body.scrollTop=body.scrollHeight;
        if(m.id){ state.lastId=Math.max(state.lastId||0,m.id); save(); }
    }

    function setStatus(online){
        dot.classList.toggle('on',!!online);
        statusText.textContent=online?'Online — we\'re here':'Offline — leave a message';
    }

    function startChat(){
        var name=foot.querySelector('#lc-name').value.trim();
        var email=foot.querySelector('#lc-email').value.trim();
        var first=foot.querySelector('#lc-first').value.trim();
        if(!first){ foot.querySelector('#lc-first').focus(); return; }
        state.name=name; state.email=email; save();
        post({name:name,email:email,body:first,page:location.pathname});
    }
    function sendMsg(){
        var ta=foot.querySelector('#lc-text'); var v=ta.value.trim(); if(!v) return;
        ta.value=''; post({token:state.token,body:v,page:location.pathname});
    }
    function post(payload){
        addMsg({sender:'visitor',body:payload.body,time:''});
        fetch(MSG,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
            body:JSON.stringify(payload)})
        .then(function(r){return r.json();})
        .then(function(d){
            if(d&&d.token){ var isNew=!state.token; state.token=d.token; save(); if(isNew){ body.innerHTML=''; renderFoot(); } }
            if(d&&d.message&&d.message.id){ state.lastId=Math.max(state.lastId||0,d.message.id); save(); }
            if(d) setStatus(d.agent_online);
        }).catch(function(){});
    }

    function pollNow(){
        if(state.token){
            fetch(POLL+'?token='+encodeURIComponent(state.token)+'&after='+(state.lastId||0),{headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                if(!d) return; setStatus(d.agent_online);
                (d.messages||[]).forEach(function(m){
                    addMsg(m);
                    if(m.sender==='admin' && !open){ showBadge(); }
                });
            }).catch(function(){});
        } else {
            fetch(STATUS,{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(function(r){return r.json();})
                .then(function(d){ if(d) setStatus(d.agent_online); }).catch(function(){});
        }
    }
    var unread=0;
    function showBadge(){ unread++; badge.textContent=unread; badge.style.display='block'; }
    function startPolling(){ stopPolling(); pollTimer=setInterval(pollNow,5000); }
    function stopPolling(){ if(pollTimer){clearInterval(pollTimer);pollTimer=null;} }

    // background poll (slow) to surface new replies even when closed
    setInterval(function(){ if(!open) pollNow(); }, 20000);
    pollNow();
})();
</script>
@endunless
