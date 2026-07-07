@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content bg-light">
            <div class="page-inner">
                <div class="mt-2 mb-4 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="text-dark font-weight-bold mb-0">Live Chat
                            <span id="lc-total-unread" class="badge badge-danger align-middle" style="display:none">0</span>
                        </h3>
                        <p class="text-muted mb-0">Reply to visitors in real time. You appear <b>Online</b> to visitors while this page is open.</p>
                    </div>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="lc-appear-offline" {{ $appear_offline ? 'checked' : '' }}>
                        <label class="custom-control-label text-dark" for="lc-appear-offline">Appear offline</label>
                    </div>
                </div>

                <div class="row">
                    {{-- Conversation list --}}
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white font-weight-bold text-dark">Conversations</div>
                            <div class="card-body p-0" id="lc-conv-list" style="max-height:60vh;overflow-y:auto">
                                <div class="text-muted text-center p-4">Loading…</div>
                            </div>
                        </div>
                    </div>

                    {{-- Thread --}}
                    <div class="col-md-8 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="font-weight-bold text-dark" id="lc-thread-name">Select a conversation</span>
                                    <small class="text-muted d-block" id="lc-thread-email"></small>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" id="lc-close-conv" style="display:none">Close chat</button>
                            </div>
                            <div class="card-body" id="lc-thread" style="height:52vh;overflow-y:auto;background:#f6f7fb">
                                <div class="text-muted text-center p-5">Pick a conversation on the left to start replying.</div>
                            </div>
                            <div class="card-footer bg-white" id="lc-reply-box" style="display:none">
                                <div class="input-group">
                                    <textarea class="form-control" id="lc-reply-text" rows="1" placeholder="Type your reply…"></textarea>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" id="lc-reply-send">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .lc-c-item{padding:12px 14px;border-bottom:1px solid #eee;cursor:pointer}
        .lc-c-item:hover{background:#f6f7fb}
        .lc-c-item.active{background:#ede4ff}
        .lc-a-msg,.lc-v-msg{max-width:78%;padding:8px 12px;border-radius:12px;margin-bottom:8px;font-size:14px;white-space:pre-wrap;word-wrap:break-word}
        .lc-v-msg{background:#fff;border:1px solid #e6e6ef;margin-right:auto;border-bottom-left-radius:3px}
        .lc-a-msg{background:linear-gradient(135deg,#b57bff,#7c3aed);color:#fff;margin-left:auto;border-bottom-right-radius:3px}
        .lc-m-time{font-size:10px;opacity:.6;display:block;margin-top:2px}
    </style>

    <script>
    (function(){
        var CSRF=document.querySelector('meta[name="csrf-token"]').content;
        var URLS={
            convs:"{{ route('livechat.conversations') }}",
            msgs:function(id){return "{{ url('admin/dashboard/live-chat') }}/"+id+"/messages";},
            reply:function(id){return "{{ url('admin/dashboard/live-chat') }}/"+id+"/reply";},
            close:function(id){return "{{ url('admin/dashboard/live-chat') }}/"+id+"/close";},
            heartbeat:"{{ route('livechat.heartbeat') }}"
        };
        var active=null, lastId=0, seen={}, sending=false;
        var list=document.getElementById('lc-conv-list'), thread=document.getElementById('lc-thread'),
            replyBox=document.getElementById('lc-reply-box'), replyText=document.getElementById('lc-reply-text'),
            sendBtn=document.getElementById('lc-reply-send');

        function esc(s){return(s||'').replace(/[&<>"]/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];});}
        function flag(cc){ if(!cc||cc.length!==2) return ''; return cc.toUpperCase().replace(/./g,function(c){return String.fromCodePoint(127397+c.charCodeAt(0));}); }

        // Render a message once (deduped by id, so the reply POST + the poll can't double it).
        function renderMsg(m){
            if(m.id && seen[m.id]) return;
            if(m.id) seen[m.id]=1;
            var el=document.createElement('div'); el.className=(m.sender==='admin'?'lc-a-msg':'lc-v-msg');
            el.innerHTML=esc(m.body)+'<span class="lc-m-time">'+esc(m.time)+'</span>';
            thread.appendChild(el);
            if(m.id) lastId=Math.max(lastId,m.id);
        }

        function loadConvs(){
            fetch(URLS.convs,{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(function(d){
                var tu=document.getElementById('lc-total-unread');
                if(d.total_unread>0){tu.textContent=d.total_unread;tu.style.display='inline-block';}else{tu.style.display='none';}
                if(!d.conversations.length){list.innerHTML='<div class="text-muted text-center p-4">No conversations yet.</div>';return;}
                list.innerHTML=d.conversations.map(function(c){
                    var loc=[]; if(c.country) loc.push(flag(c.country_code)+' '+esc(c.country)); if(c.ip) loc.push(esc(c.ip));
                    return '<div class="lc-c-item '+(active===c.id?'active':'')+'" data-id="'+c.id+'">'+
                        '<div class="d-flex justify-content-between"><b class="text-dark">'+esc(c.name)+'</b>'+
                        (c.unread>0?'<span class="badge badge-danger">'+c.unread+'</span>':'')+'</div>'+
                        '<div class="text-muted small text-truncate">'+esc(c.snippet)+'</div>'+
                        (loc.length?'<div class="text-muted" style="font-size:11px">'+loc.join(' · ')+'</div>':'')+
                        '<div class="text-muted" style="font-size:11px">'+esc(c.email||'')+' · '+esc(c.time||'')+'</div></div>';
                }).join('');
                Array.prototype.forEach.call(list.querySelectorAll('.lc-c-item'),function(el){
                    el.addEventListener('click',function(){selectConv(parseInt(el.dataset.id));});
                });
            }).catch(function(){});
        }

        function selectConv(id){
            if(active!==id){active=id;lastId=0;seen={};thread.innerHTML='';}
            document.getElementById('lc-close-conv').style.display='inline-block';
            replyBox.style.display='block';
            loadMsgs(true);
        }

        function loadMsgs(scroll){
            if(!active) return;
            fetch(URLS.msgs(active)+'?after='+lastId,{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(function(d){
                var c=d.conversation||{};
                document.getElementById('lc-thread-name').textContent=(c.country?flag(c.country_code)+' ':'')+(c.name||'Guest');
                var meta=[]; if(c.email) meta.push(c.email);
                if(c.country) meta.push(c.country); if(c.ip) meta.push('IP '+c.ip);
                if(c.page) meta.push(c.page);
                document.getElementById('lc-thread-email').textContent=meta.join(' · ');
                if(lastId===0 && !d.messages.length){thread.innerHTML='<div class="text-muted text-center p-4">No messages yet.</div>';}
                if(d.messages.length && lastId===0 && thread.querySelector('.text-muted')){thread.innerHTML='';}
                d.messages.forEach(renderMsg);
                if(scroll||d.messages.length) thread.scrollTop=thread.scrollHeight;
            }).catch(function(){});
        }

        function sendReply(){
            var v=replyText.value.trim(); if(!v||!active||sending) return;
            sending=true; sendBtn.disabled=true; replyText.value='';
            fetch(URLS.reply(active),{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
                body:JSON.stringify({body:v})}).then(r=>r.json()).then(function(d){
                if(d&&d.message){ renderMsg(d.message); thread.scrollTop=thread.scrollHeight; }
            }).catch(function(){}).finally(function(){ sending=false; sendBtn.disabled=false; });
        }

        document.getElementById('lc-reply-send').addEventListener('click',sendReply);
        replyText.addEventListener('keydown',function(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendReply();}});
        document.getElementById('lc-close-conv').addEventListener('click',function(){
            if(!active)return;
            fetch(URLS.close(active),{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'}}).then(function(){loadConvs();});
        });
        document.getElementById('lc-appear-offline').addEventListener('change',function(){
            var body='appear_offline='+(this.checked?'1':'0');
            fetch(URLS.heartbeat,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},body:body});
        });

        // Polling: conversations keep the heartbeat fresh (agent shows Online to visitors)
        loadConvs(); setInterval(loadConvs,5000);
        setInterval(function(){ if(active) loadMsgs(false); },4000);
    })();
    </script>
@endsection
