{{-- Widget chatbot IA : bouton flottant + fenêtre togglable. Rendu dans un iframe isolé (shell sans layout).
     IMPORTANT : position:fixed (pas absolute) — le body de l'iframe n'a pas de hauteur, un conteneur
     height:100% vaudrait 0 et le bouton ancré bottom sortirait du cadre (bug du widget invisible).
     La conversation est persistée en SESSION côté serveur : elle survit aux refreshs/navigations. --}}
<div id="ai-chatbot-root" style="background:transparent;">
    {{-- Fenêtre de chat (cachée par défaut) --}}
    <div id="ai-chat-window" style="position:fixed;bottom:84px;right:14px;width:360px;max-width:calc(100vw - 2rem);height:440px;max-height:calc(100vh - 2rem);display:none;flex-direction:column;background:rgba(255,255,255,0.96);border-radius:24px;box-shadow:0 20px 60px rgba(15,23,42,0.25);border:1px solid rgba(148,163,184,0.35);overflow:hidden;pointer-events:auto;z-index:10;">
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

        <div id="ai-chat-messages" style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px;background:transparent;">
            <div style="align-self:flex-start;max-width:85%;background:#f1f5f9;color:#334155;border-radius:16px;border-top-left-radius:4px;padding:10px 14px;font-size:13px;line-height:1.5;white-space:pre-wrap;">{{ __('ai.greeting') }}</div>
            {{-- Chips de questions suggerees : retirees apres le premier message envoye --}}
            <div id="ai-chat-suggestions" style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach([
                    __('ai.sug_summary'),
                    __('ai.sug_recurring'),
                    __('ai.sug_top'),
                    __('ai.sug_trend'),
                ] as $sug)
                <button type="button" class="ai-sug" data-msg="{{ $sug }}" style="border:1px solid rgba(79,70,229,0.35);background:rgba(79,70,229,0.06);color:#4f46e5;border-radius:14px;padding:6px 12px;font-size:12px;cursor:pointer;font-family:inherit;">{{ $sug }}</button>
                @endforeach
            </div>
        </div>

        <div style="padding:10px;border-top:1px solid rgba(148,163,184,0.25);background:#fff;">
            <form id="ai-chat-form" style="display:flex;gap:8px;align-items:flex-end;">
                <textarea id="ai-chat-input" rows="1" placeholder="{{ __('ai.placeholder') }}" style="flex:1;resize:none;border:1px solid rgba(148,163,184,0.45);border-radius:14px;padding:10px 14px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;color:#334155;"></textarea>
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
    var open = false;

    // Mini rendu Markdown SAFE : on echappe TOUT le HTML d'abord (anti-XSS), puis on
    // transforme uniquement gras/italique/code/listes/TABLEAUX en balises controlees.
    function inlineMd(h) {
        h = h.replace(/`([^`]+)`/g, '<code style="background:rgba(148,163,184,0.2);border-radius:4px;padding:1px 5px;font-size:12px;">$1</code>');
        h = h.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        h = h.replace(/(^|\s)\*([^*\n]+)\*(?=\s|$|[,.;:!?])/g, '$1<em>$2</em>');
        h = h.replace(/^- (.+)$/gm, '• $1');
        return h;
    }

    function renderTable(lines) {
        // lines : lignes brutes du tableau Markdown (deja echappees). Ligne 2 = |---|---| ignoree.
        var rows = [];
        for (var i = 0; i < lines.length; i++) {
            var t = lines[i].trim();
            if (/^\|?\s*:?-{2,}/.test(t.replace(/\|/g, ' ').trim()) && /^[\s|:-]+$/.test(t)) continue; // separateur
            var cells = t.replace(/^\|/, '').replace(/\|$/, '').split('|');
            var row = [];
            for (var j = 0; j < cells.length; j++) row.push(cells[j].trim());
            rows.push(row);
        }
        if (!rows.length) return '';
        var html = '<table style="border-collapse:collapse;width:100%;margin:6px 0;font-size:12px;">';
        for (var r = 0; r < rows.length; r++) {
            var tag = (r === 0) ? 'th' : 'td';
            html += '<tr>';
            for (var c = 0; c < rows[r].length; c++) {
                var style = 'border:1px solid rgba(148,163,184,0.4);padding:5px 8px;text-align:' +
                    (c === 0 ? 'left' : 'right') + ';';
                if (r === 0) style += 'background:rgba(79,70,229,0.12);font-weight:700;color:#4f46e5;';
                html += '<' + tag + ' style="' + style + '">' + inlineMd(rows[r][c]) + '</' + tag + '>';
            }
            html += '</tr>';
        }
        html += '</table>';
        return html;
    }

    function md(text) {
        var esc = document.createElement('div');
        esc.textContent = text == null ? '' : String(text);
        var h = esc.innerHTML;
        // Decoupe en blocs : lignes de tableau Markdown | a | b | d'un cote, texte de l'autre
        var out = '';
        var lines = h.split('\n');
        var tableBuf = [];
        function flushTable() {
            if (tableBuf.length) {
                out += renderTable(tableBuf);
                tableBuf = [];
            }
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
        // Nettoie les <br> en trop
        out = out.replace(/(<br>)+/g, '<br>').replace(/^<br>|<br>$/g, '');
        return out;
    }

    function bubbleEl(role) {
        var wrap = document.createElement('div');
        wrap.style.cssText = 'display:flex;' + (role === 'user' ? 'justify-content:flex-end;' : 'justify-content:flex-start;');
        var b = document.createElement('div');
        b.style.cssText = 'max-width:85%;padding:10px 14px;font-size:13px;line-height:1.55;border-radius:16px;word-wrap:break-word;' +
            (role === 'user'
                ? 'background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-top-right-radius:4px;white-space:pre-wrap;'
                : 'background:#f1f5f9;color:#334155;border-top-left-radius:4px;');
        wrap.appendChild(b);
        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
        return b;
    }

    function addBubble(text, role) {
        var b = bubbleEl(role);
        if (role === 'assistant') { b.innerHTML = md(text); }
        else { b.textContent = text; }
        box.scrollTop = box.scrollHeight;
    }

    function hideSuggestions() {
        if (sugs) { sugs.remove(); sugs = null; }
    }

    function thinkingEl() {
        var wrap = document.createElement('div');
        wrap.style.cssText = 'display:flex;justify-content:flex-start;';
        var t = document.createElement('div');
        t.style.cssText = 'background:#f1f5f9;color:#94a3b8;border-radius:16px;border-top-left-radius:4px;padding:10px 14px;font-size:13px;display:flex;gap:4px;align-items:center;';
        t.innerHTML = '<span class="ai-dot" style="width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:aiBlink 1.2s infinite;"></span>'
            + '<span class="ai-dot" style="width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:aiBlink 1.2s infinite 0.2s;"></span>'
            + '<span class="ai-dot" style="width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:aiBlink 1.2s infinite 0.4s;"></span>';
        wrap.appendChild(t);
        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
        return { wrap: wrap, dot: t };
    }

    // Injection de l'animation (les <style> inline ne peuvent pas porter @keyframes)
    var st = document.createElement('style');
    st.textContent = '@keyframes aiBlink{0%,80%,100%{opacity:.25}40%{opacity:1}}';
    document.head.appendChild(st);

    function setOpen(v) {
        open = v;
        win.style.display = v ? 'flex' : 'none';
        // Agrandit/reduit l iframe parent pour couvrir le chat (le bouton seul = 100x100)
        try {
            parent.postMessage({ aiChatbot: v ? 'open' : 'close' }, '*');
        } catch (e) {}
    }

    toggle.addEventListener('click', function () { setOpen(!open); });
    closeBtn.addEventListener('click', function () { setOpen(false); });

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function send(msg) {
        msg = (msg || input.value).trim();
        if (!msg) return;
        hideSuggestions();
        addBubble(msg, 'user');
        input.value = '';
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
            th.wrap.remove();
            var reply = data.reply || data.error || '{{ __('ai.no_response') }}';
            addBubble(reply, 'assistant');
        })
        .catch(function () {
            th.wrap.remove();
            addBubble('{{ __('ai.api_error') }}', 'assistant');
        })
        .finally(function () { sendBtn.disabled = false; });
    }

    clearBtn.addEventListener('click', function () {
        // Supprime les bulles cote client puis vide la session cote serveur
        while (box.children.length > 1) box.removeChild(box.lastChild);
        hideSuggestions();
        box.appendChild(sugs);
        fetch('/api/chatbot/clear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            credentials: 'same-origin'
        });
    });

    if (sugs) {
        sugs.addEventListener('click', function (e) {
            var b = e.target.closest('.ai-sug');
            if (b) send(b.getAttribute('data-msg'));
        });
    }

    form.addEventListener('submit', function (e) { e.preventDefault(); send(); });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });

    // Historique serveur : rejoue les bulles au chargement; s'il y a deja une conversation,
    // les chips de suggestion ne se montrent pas.
    var serverHistory = @json($chatHistory ?? []);
    if (serverHistory.length > 0) {
        hideSuggestions();
        serverHistory.forEach(function (m) {
            if (m && m.content) addBubble(m.content, m.role === 'user' ? 'user' : 'assistant');
        });
    }
})();
</script>
