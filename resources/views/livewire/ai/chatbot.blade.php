{{-- Widget chatbot IA : bouton flottant + fenêtre togglable. Rendu dans un iframe isolé (shell sans layout).
     position:fixed obligatoire (le body de l'iframe n'a pas de hauteur).
     Historique en CACHE serveur (24h) — survit aux refreshs/navigations.
     UX v2 : animations d'ouverture, dark mode, copier réponse, textarea auto-resize, tableaux scrollables. --}}
<div id="ai-chatbot-root" style="background:transparent;">
    {{-- Fenêtre de chat (cachée par défaut) --}}
    <div id="ai-chat-window" class="ai-window" style="position:fixed;bottom:84px;right:14px;width:360px;max-width:calc(100vw - 2rem);height:460px;max-height:calc(100vh - 2rem);display:none;flex-direction:column;background:rgba(255,255,255,0.97);border-radius:24px;box-shadow:0 20px 60px rgba(15,23,42,0.25);border:1px solid rgba(148,163,184,0.35);overflow:hidden;pointer-events:auto;z-index:10;">
        <div style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;">
            <div style="width:36px;height:36px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:18px;">🤖</div>
            <div style="flex:1;">
                <div style="font-weight:700;font-size:15px;">{{ __('ai.title') }}</div>
                <div style="font-size:11px;opacity:0.85;">{{ __('ai.subtitle') }}</div>
            </div>
            <button id="ai-chat-clear" type="button" title="{{ __('ai.clear') }}" style="background:none;border:0;color:#fff;cursor:pointer;padding:6px;border-radius:10px;display:flex;opacity:0.85;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
            <button id="ai-chat-close" type="button" style="background:none;border:0;color:#fff;cursor:pointer;padding:6px;border-radius:10px;display:flex;">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="ai-chat-messages" class="ai-scroll" style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px;background:transparent;">
            @if(!empty($authRequired))
            <div style="align-self:center;text-align:center;background:#fef3c7;color:#92400e;border-radius:16px;padding:14px 18px;font-size:13px;line-height:1.6;max-width:90%;">
                ⏳ {{ __('ai.session_expired') }}<br>
                <button id="ai-chat-reload" type="button" style="margin-top:8px;border:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;padding:7px 16px;font-size:12px;cursor:pointer;font-family:inherit;">{{ __('ai.reload_page') }}</button>
            </div>
            @else
            <div class="ai-bubble-ai">{{ $greeting }}</div>
            {{-- Chips de questions suggerees : retirees apres le premier message envoye --}}
            <div id="ai-chat-suggestions" style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach([
                    __('ai.sug_summary'),
                    __('ai.sug_recurring'),
                    __('ai.sug_top'),
                    __('ai.sug_trend'),
                ] as $sug)
                <button type="button" class="ai-sug" data-msg="{{ $sug }}">{{ $sug }}</button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Scroll-to-bottom flottant --}}
        <button id="ai-chat-scroll" type="button" title="↓" style="position:absolute;bottom:86px;right:16px;width:32px;height:32px;border-radius:50%;border:1px solid rgba(148,163,184,0.4);background:#fff;color:#4f46e5;box-shadow:0 4px 14px rgba(15,23,42,0.18);cursor:pointer;display:none;align-items:center;justify-content:center;z-index:5;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </button>

        <div style="padding:10px;border-top:1px solid rgba(148,163,184,0.25);background:#fff;" class="ai-inputbar">
            <form id="ai-chat-form" style="display:flex;gap:8px;align-items:flex-end;">
                <textarea id="ai-chat-input" rows="1" placeholder="{{ __('ai.placeholder') }}" style="flex:1;resize:none;border:1px solid rgba(148,163,184,0.45);border-radius:14px;padding:10px 14px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;color:#334155;max-height:96px;"></textarea>
                <button type="submit" id="ai-chat-send" style="width:40px;height:40px;border-radius:14px;border:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Bouton flottant --}}
    <button id="ai-chatbot-toggle" type="button" aria-label="{{ __('ai.title') }}"
        style="position:fixed;bottom:14px;right:14px;width:56px;height:56px;border-radius:50%;border:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 10px 30px rgba(79,70,229,0.45);cursor:pointer;display:flex;align-items:center;justify-content:center;pointer-events:auto;z-index:10;">
        <svg id="ai-chat-icon" style="width:26px;height:26px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
    </button>
</div>

<style>
    /* ---------- Dark mode : .dark sur <html> pose par le shell (localStorage.theme) ---------- */
    .dark #ai-chat-window { background:rgba(15,23,42,0.97) !important; border-color:rgba(51,65,85,0.6) !important; }
    .dark .ai-bubble-ai  { background:#1e293b !important; color:#cbd5e1 !important; }
    .dark .ai-bubble-user{ color:#fff !important; }
    .dark .ai-inputbar   { background:#0f172a !important; border-top-color:rgba(51,65,85,0.6) !important; }
    .dark #ai-chat-input { background:#1e293b !important; color:#e2e8f0 !important; border-color:rgba(71,85,105,0.7) !important; }
    .dark .ai-sug        { background:rgba(129,140,248,0.12) !important; color:#a5b4fc !important; border-color:rgba(129,140,248,0.35) !important; }
    .dark .ai-scroll-wrap { background:transparent !important; }
    .dark #ai-chat-scroll{ background:#1e293b !important; color:#a5b4fc !important; }
    .dark .ai-copy-btn   { background:#1e293b !important; color:#94a3b8 !important; }
    .dark .ai-copy-btn:hover { background:#334155 !important; color:#e2e8f0 !important; }
    .dark .ai-table th   { background:rgba(99,102,241,0.25) !important; color:#c7d2fe !important; }
    .dark .ai-table td   { border-color:rgba(71,85,105,0.7) !important; color:#cbd5e1 !important; }

    /* ---------- Bulles ---------- */
    .ai-bubble-ai, .ai-bubble-user {
        max-width:85%; padding:10px 14px; font-size:13px; line-height:1.55;
        border-radius:16px; word-wrap:break-word;
    }
    .ai-bubble-ai  { align-self:flex-start; background:#f1f5f9; color:#334155; border-top-left-radius:4px; }
    .ai-bubble-user{ align-self:flex-end; background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; border-top-right-radius:4px; white-space:pre-wrap; }

    /* ---------- Chips suggestions ---------- */
    .ai-sug { border:1px solid rgba(79,70,229,0.35); background:rgba(79,70,229,0.06); color:#4f46e5;
        border-radius:14px; padding:6px 12px; font-size:12px; cursor:pointer; font-family:inherit;
        transition:background .15s, transform .15s; }
    .ai-sug:hover { background:rgba(79,70,229,0.14); transform:translateY(-1px); }

    /* ---------- Bouton copier ---------- */
    .ai-msg-wrap { display:flex; flex-direction:column; max-width:85%; }
    .ai-msg-wrap.left { align-self:flex-start; }
    .ai-copy-btn { align-self:flex-start; margin-top:2px; border:0; background:transparent; color:#94a3b8;
        font-size:11px; cursor:pointer; display:flex; align-items:center; gap:4px; padding:2px 6px;
        border-radius:8px; opacity:0; transition:opacity .15s; font-family:inherit; }
    .ai-msg-wrap:hover .ai-copy-btn { opacity:1; }
    .ai-copy-btn:hover { background:#f1f5f9; }

    /* ---------- Tableaux scrollables horizontalement ---------- */
    .ai-table-wrap { overflow-x:auto; max-width:100%; margin:6px 0; border-radius:10px; }
    .ai-table { border-collapse:collapse; width:100%; min-width:280px; font-size:12px; }
    .ai-table th, .ai-table td { border:1px solid rgba(148,163,184,0.4); padding:5px 8px; white-space:nowrap; }
    .ai-table th { background:rgba(79,70,229,0.12); font-weight:700; color:#4f46e5; text-align:left; }
    .ai-table td { text-align:right; }
    .ai-table td:first-child { text-align:left; }

    /* ---------- Animations ---------- */
    @keyframes aiPop { from { opacity:0; transform:translateY(12px) scale(0.96); } to { opacity:1; transform:none; } }
    .ai-window.open { display:flex !important; animation:aiPop .22s cubic-bezier(.2,.9,.3,1.2); }
    @keyframes aiBubbleIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
    .ai-bubble-ai, .ai-bubble-user, .ai-msg-wrap { animation:aiBubbleIn .18s ease-out; }
    @keyframes aiPulse { 0%,100% { box-shadow:0 10px 30px rgba(79,70,229,0.45); } 50% { box-shadow:0 10px 30px rgba(79,70,229,0.9); } }
    #ai-chatbot-toggle.pulse { animation:aiPulse 1.4s ease-in-out 3; }
    @keyframes aiBlink { 0%,80%,100% { opacity:.25 } 40% { opacity:1 } }

    /* ---------- Scrollbar discrete ---------- */
    .ai-scroll::-webkit-scrollbar { width:6px; }
    .ai-scroll::-webkit-scrollbar-thumb { background:rgba(148,163,184,0.5); border-radius:3px; }
</style>

<script>
(function () {
    var toggle = document.getElementById('ai-chatbot-toggle');
    var win = document.getElementById('ai-chat-window');
    var closeBtn = document.getElementById('ai-chat-close');
    var clearBtn = document.getElementById('ai-chat-clear');
    var form = document.getElementById('ai-chat-form');
    var input = document.getElementById('ai-chat-input');
    var box = document.getElementById('ai-chat-messages');
    var sendBtn = document.getElementById('ai-chat-send');
    var sugs = document.getElementById('ai-chat-suggestions');
    var scrollBtn = document.getElementById('ai-chat-scroll');
    var open = false;
    var unreadPending = false;

    // Mini rendu Markdown SAFE : echappement HTML integral d'abord (anti-XSS), puis
    // gras/italique/code/listes/TABLEAUX en balises controlees uniquement.
    function inlineMd(h) {
        h = h.replace(/`([^`]+)`/g, '<code style="background:rgba(148,163,184,0.25);border-radius:4px;padding:1px 5px;font-size:12px;">$1</code>');
        h = h.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        h = h.replace(/(^|\s)\*([^*\n]+)\*(?=\s|$|[,.;:!?])/g, '$1<em>$2</em>');
        h = h.replace(/^- (.+)$/gm, '• $1');
        return h;
    }

    function renderTable(lines) {
        var rows = [];
        for (var i = 0; i < lines.length; i++) {
            var t = lines[i].trim();
            if (/^\|?\s*:?-{2,}/.test(t.replace(/\|/g, ' ').trim()) && /^[\s|:-]+$/.test(t)) continue;
            var cells = t.replace(/^\|/, '').replace(/\|$/, '').split('|');
            var row = [];
            for (var j = 0; j < cells.length; j++) row.push(cells[j].trim());
            rows.push(row);
        }
        if (!rows.length) return '';
        var html = '<div class="ai-table-wrap"><table class="ai-table">';
        for (var r = 0; r < rows.length; r++) {
            var tag = (r === 0) ? 'th' : 'td';
            html += '<tr>';
            for (var c = 0; c < rows[r].length; c++) {
                html += '<' + tag + '>' + inlineMd(rows[r][c]) + '</' + tag + '>';
            }
            html += '</tr>';
        }
        html += '</table></div>';
        return html;
    }

    function md(text) {
        var esc = document.createElement('div');
        esc.textContent = text == null ? '' : String(text);
        var h = esc.innerHTML;
        var out = '';
        var lines = h.split('\n');
        var tableBuf = [];
        function flushTable() {
            if (tableBuf.length) { out += renderTable(tableBuf); tableBuf = []; }
        }
        for (var i = 0; i < lines.length; i++) {
            var line = lines[i];
            var trimmed = line.trim();
            if (trimmed.charAt(0) === '|' || (trimmed.indexOf('|') !== -1 && trimmed.indexOf('|') !== trimmed.lastIndexOf('|') && trimmed.split('|').length >= 3 && i + 1 < lines.length && /^\s*\|?[\s:|-]+\|[\s:|-]*$/.test(lines[i + 1] || ''))) {
                tableBuf.push(line);
                continue;
            }
            flushTable();
            out += inlineMd(line) + '<br>';
        }
        flushTable();
        out = out.replace(/(<br>)+/g, '<br>').replace(/^<br>|<br>$/g, '');
        return out;
    }

    // Construit une bulle + bouton copier (les reponses IA sont copiables)
    function bubbleEl(role, text) {
        var wrap = document.createElement('div');
        wrap.className = 'ai-msg-wrap ' + (role === 'user' ? 'right' : 'left');

        var b = document.createElement('div');
        b.className = role === 'user' ? 'ai-bubble-user' : 'ai-bubble-ai';
        if (role === 'assistant') { b.innerHTML = md(text); }
        else { b.textContent = text; }
        wrap.appendChild(b);

        if (role === 'assistant') {
            var cp = document.createElement('button');
            cp.type = 'button';
            cp.className = 'ai-copy-btn';
            cp.innerHTML = '<svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Copier';
            cp.addEventListener('click', function () {
                navigator.clipboard.writeText(text || b.textContent).then(function () {
                    cp.innerHTML = '<svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copié';
                    setTimeout(function () {
                        cp.innerHTML = '<svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Copier';
                    }, 1600);
                });
            });
            wrap.appendChild(cp);
        }

        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
        return wrap;
    }

    function addBubble(text, role) { bubbleEl(role, text); }

    function hideSuggestions() {
        if (sugs) { sugs.remove(); sugs = null; }
    }

    function thinkingEl() {
        var wrap = document.createElement('div');
        wrap.className = 'ai-msg-wrap left';
        var t = document.createElement('div');
        t.className = 'ai-bubble-ai';
        t.innerHTML = '<span class="ai-dot" style="width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:aiBlink 1.2s infinite;"></span>'
            + '<span class="ai-dot" style="width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:aiBlink 1.2s infinite 0.2s;"></span>'
            + '<span class="ai-dot" style="width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:aiBlink 1.2s infinite 0.4s;"></span>';
        wrap.appendChild(t);
        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
        return wrap;
    }

    function setOpen(v) {
        open = v;
        if (v) {
            win.classList.add('open');
            unreadPending = false;
            setTimeout(function () { input.focus(); }, 120);
        } else {
            win.classList.remove('open');
        }
        // Agrandit/reduit l iframe parent pour couvrir le chat (le bouton seul = 100x100)
        try {
            parent.postMessage({ aiChatbot: v ? 'open' : 'close' }, '*');
        } catch (e) {}
    }

    toggle.addEventListener('click', function () { setOpen(!open); });
    closeBtn.addEventListener('click', function () { setOpen(false); });

    // Mode degrade : session expiree -> le bouton recharge la page PARENTE (l iframe est dedans)
    var reloadBtn = document.getElementById('ai-chat-reload');
    if (reloadBtn) {
        reloadBtn.addEventListener('click', function () {
            try { parent.location.reload(); } catch (e) { window.location.reload(); }
        });
    }

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // Auto-resize du textarea (1 -> 4 lignes)
    input.addEventListener('input', function () {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 96) + 'px';
    });

    // Scroll-to-bottom flottant
    box.addEventListener('scroll', function () {
        var nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 60;
        scrollBtn.style.display = nearBottom ? 'none' : 'flex';
    });
    scrollBtn.addEventListener('click', function () {
        box.scrollTop = box.scrollHeight;
        scrollBtn.style.display = 'none';
    });

    function send(msg) {
        msg = (msg || input.value).trim();
        if (!msg) return;
        hideSuggestions();
        addBubble(msg, 'user');
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;
        var th = thinkingEl();

        fetch('/api/chatbot', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            credentials: 'same-origin',
            body: JSON.stringify({ message: msg })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            th.remove();
            var reply = data.reply || data.error || '{{ __('ai.no_response') }}';
            addBubble(reply, 'assistant');
            if (!open) {
                unreadPending = true;
                toggle.classList.add('pulse');
            }
        })
        .catch(function () {
            th.remove();
            addBubble('{{ __('ai.api_error') }}', 'assistant');
        })
        .finally(function () {
            sendBtn.disabled = false;
            scrollBtn.style.display = 'none';
        });
    }

    clearBtn.addEventListener('click', function () {
        while (box.children.length > 1) box.removeChild(box.lastChild);
        hideSuggestions();
        // Recree les chips apres effacement
        var div = document.createElement('div');
        div.id = 'ai-chat-suggestions';
        div.style.cssText = 'display:flex;flex-wrap:wrap;gap:6px;';
        @foreach([
            __('ai.sug_summary'),
            __('ai.sug_recurring'),
            __('ai.sug_top'),
            __('ai.sug_trend'),
        ] as $sug)
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'ai-sug';
        b.setAttribute('data-msg', @js($sug));
        b.textContent = @js($sug);
        div.appendChild(b);
        @endforeach
        box.appendChild(div);
        sugs = div;
        bindSugs();
        fetch('/api/chatbot/clear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            credentials: 'same-origin'
        });
    });

    function bindSugs() {
        if (!sugs) return;
        sugs.addEventListener('click', function (e) {
            var b = e.target.closest('.ai-sug');
            if (b) send(b.getAttribute('data-msg'));
        });
    }
    bindSugs();

    form.addEventListener('submit', function (e) { e.preventDefault(); send(); });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });

    // Historique serveur : rejoue les bulles au chargement; chips masquees si conversation existante
    var serverHistory = @json($chatHistory ?? []);
    if (serverHistory.length > 0) {
        hideSuggestions();
        serverHistory.forEach(function (m) {
            if (m && m.content) addBubble(m.content, m.role === 'user' ? 'user' : 'assistant');
        });
    }
})();
</script>
