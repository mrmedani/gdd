{{-- Widget chatbot IA : bouton flottant + fenêtre togglable. Rendu dans un iframe isolé (shell sans layout).
     position:fixed obligatoire (le body de l'iframe n'a pas de hauteur).
     Historique en CACHE serveur (24h) — survit aux refreshs/navigations.
     UX v2 : animations d'ouverture, dark mode, copier réponse, textarea auto-resize, tableaux scrollables.
     UX v3 : TOUT est configurable depuis /settings via $aiCfg (nom, emoji, salutation, chips,
     palette, position, taille, auto-open, on/off, message offline). --}}
@php
    $cfg = $aiCfg ?? [];
    $aiNameFinal = ($cfg['name'] ?? '') !== '' ? $cfg['name'] : $assistantName;
    $aiEmojiFinal = $cfg['emoji'] ?? '🤖';
    // Palettes : [gradient bouton/header, couleur accent texte chips/tableaux]
    $palettes = [
        'indigo'  => ['linear-gradient(135deg,#4f46e5,#7c3aed)', '#4f46e5'],
        'emerald' => ['linear-gradient(135deg,#059669,#10b981)', '#059669'],
        'ocean'   => ['linear-gradient(135deg,#0284c7,#2563eb)', '#0284c7'],
        'sunset'  => ['linear-gradient(135deg,#ea580c,#f59e0b)', '#ea580c'],
        'slate'   => ['linear-gradient(135deg,#334155,#475569)', '#334155'],
        'rose'    => ['linear-gradient(135deg,#e11d48,#f43f5e)', '#e11d48'],
    ];
    $pal = $palettes[$cfg['palette'] ?? 'indigo'] ?? $palettes['indigo'];
    $gradient = $pal[0];
    $accent = $pal[1];
    // Palette : couleur RGB de la couleur active pour les ombres/pulse (sinon indigo fixe)
    $accentRgb = match ($cfg['palette'] ?? 'indigo') {
        'emerald' => '5,150,105',
        'ocean'   => '2,132,199',
        'sunset'  => '234,88,12',
        'slate'   => '51,65,85',
        'rose'    => '225,29,72',
        default   => '79,70,229',
    };
    $posSide = ($cfg['position'] ?? 'right') === 'left' ? 'left' : 'right';
    $winLarge = ($cfg['size'] ?? 'normal') === 'large';
    // Source unique des chips pour le chargement ET le clear (custom sinon defaut traduit)
    $chipsForJs = !empty($cfg['showSuggestions'])
        ? (!empty($cfg['suggestions'])
            ? $cfg['suggestions']
            : [__('ai.sug_summary'), __('ai.sug_recurring'), __('ai.sug_top'), __('ai.sug_trend')])
        : [];
@endphp
<div id="ai-chatbot-root" style="background:transparent;">
    {{-- Fenêtre de chat (cachée par défaut) — Design 2026 : flat, accents solides, zéro dégradé --}}
    {{-- Fenêtre de chat — RESPONSIVE : plein écran sur mobile (<768px), fenêtre adaptée sur desktop --}}
    <div id="ai-chat-window" class="ai-window" style="position:fixed;bottom:88px;{{ $posSide }}:14px;width:520px;max-width:calc(100vw - 2rem);height:{{ $winLarge ? '640px' : '560px' }};max-height:calc(100vh - 120px);display:none;flex-direction:column;background:#ffffff;border-radius:20px;box-shadow:0 24px 70px -12px rgba(15,23,42,0.35);border:1px solid rgba(148,163,184,0.30);overflow:hidden;pointer-events:auto;z-index:10;">
        <div style="display:flex;align-items:center;gap:10px;padding:12px 12px;background:#fff;border-bottom:1px solid rgba(148,163,184,0.22);">
            <div class="ai-avatar" style="width:38px;height:38px;border-radius:14px;background:{{ $accent }};display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 4px 14px rgba({{$accentRgb}},0.35);flex-shrink:0;">{{ $aiEmojiFinal }}</div>
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:2px;">
                <div style="font-weight:800;font-size:15px;color:#0f172a;letter-spacing:-0.01em;line-height:1.2;">{{ $aiNameFinal }}</div>
                <div style="font-size:11px;color:#10b981;font-weight:600;display:flex;align-items:center;gap:6px;line-height:1;">
                    <span class="ai-status-dot" style="flex-shrink:0;"></span><span>{{ __('ai.subtitle') }}</span>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                <button id="ai-chat-sessions" type="button" title="{{ __('ai.sessions') }}" class="ai-iconbtn">
                    <svg style="width:17px;height:17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h12M8 18h6M3.5 6h.01M3.5 12h.01M3.5 18h.01"/></svg>
                </button>
                <button id="ai-chat-clear" type="button" title="{{ __('ai.clear') }}" class="ai-iconbtn">
                    <svg style="width:17px;height:17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                <button id="ai-chat-close" type="button" class="ai-iconbtn">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="ai-chat-messages" class="ai-scroll" style="flex:1;overflow-y:auto;padding:16px 14px 20px;display:flex;flex-direction:column;gap:12px;background:#f8fafc;justify-content:flex-end;">
            @if(!empty($authRequired))
            <div style="align-self:center;text-align:center;background:#fef3c7;color:#92400e;border-radius:16px;padding:14px 18px;font-size:13px;line-height:1.6;max-width:90%;">
                ⏳ {{ __('ai.session_expired') }}<br>
                <button id="ai-chat-reload" type="button" style="margin-top:8px;border:0;background:{{ $accent }};color:#fff;border-radius:10px;padding:7px 16px;font-size:12px;cursor:pointer;font-family:inherit;box-shadow:0 2px 8px rgba({{$accentRgb}},0.35);">{{ __('ai.reload_page') }}</button>
            </div>
            @else
            @if(!empty($greeting))
            <div class="ai-bubble-ai" dir="auto">{{ $greeting }}</div>
            @endif
            {{-- Chips de questions suggerees seulement si session VIDE (comme le greeting).
                 Sinon la conversation active reprend sans doublon. --}}
            @if(empty($chatHistory) && !empty($cfg['showSuggestions']))
            <div id="ai-chat-suggestions" style="display:flex;flex-wrap:wrap;gap:7px;">
                @forelse(($cfg['suggestions'] ?? []) as $sug)
                <button type="button" class="ai-sug" data-msg="{{ $sug }}">{{ $sug }}</button>
                @empty
                @foreach([
                    __('ai.sug_summary'),
                    __('ai.sug_recurring'),
                    __('ai.sug_top'),
                    __('ai.sug_trend'),
                ] as $sug)
                <button type="button" class="ai-sug" data-msg="{{ $sug }}">{{ $sug }}</button>
                @endforeach
                @endforelse
            </div>
            @endif
            @endif
        </div>

        {{-- Scroll-to-bottom flottant --}}
        <button id="ai-chat-scroll" type="button" title="↓" class="ai-fab-scroll">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </button>

        <div style="padding:12px 14px 14px;border-top:1px solid rgba(148,163,184,0.22);background:#fff;" class="ai-inputbar">
            <form id="ai-chat-form" style="display:flex;gap:8px;align-items:flex-end;">
                <textarea id="ai-chat-input" rows="1" maxlength="2000" placeholder="{{ __('ai.placeholder') }}" style="flex:1;resize:none;border:1.5px solid rgba(148,163,184,0.45);border-radius:18px;padding:11px 16px;font-size:13.5px;font-family:inherit;outline:none;background:#f8fafc;color:#0f172a;max-height:104px;transition:border-color .15s, box-shadow .15s;"></textarea>
                <button type="submit" id="ai-chat-send" class="ai-send" aria-label="→">
                    <svg id="ai-send-icon" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
                    <svg id="ai-stop-icon" style="width:14px;height:14px;display:none;" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                </button>
            </form>
        </div>

        {{-- Drawer des sessions (caché par défaut) --}}
        <div id="ai-sessions-drawer" style="position:absolute;inset:0;z-index:20;display:none;flex-direction:column;background:#f8fafc;">
            <div style="display:flex;align-items:center;gap:8px;padding:12px 14px;background:#fff;border-bottom:1px solid rgba(148,163,184,0.22);">
                <button id="ai-sessions-back" type="button" class="ai-iconbtn">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </button>
                <div style="flex:1;font-weight:800;font-size:14px;color:#0f172a;">{{ __('ai.sessions') }}</div>
                <button id="ai-sessions-new" type="button" title="{{ __('ai.new_session') }}" style="background:{{ $accent }};border:0;color:#fff;cursor:pointer;padding:7px 14px;border-radius:12px;font-size:12px;font-weight:700;font-family:inherit;display:flex;align-items:center;gap:5px;box-shadow:0 3px 10px rgba({{$accentRgb}},0.35);">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    {{ __('ai.new_session') }}
                </button>
            </div>
            <div id="ai-sessions-list" class="ai-scroll" style="flex:1;overflow-y:auto;padding:10px;"></div>
        </div>
    </div>

    {{-- Bouton flottant — 2026 : accent solide + anneau lumineux au hover --}}
    <button id="ai-chatbot-toggle" type="button" aria-label="{{ __('ai.title') }}"
        style="position:fixed;bottom:14px;{{ $posSide }}:14px;width:58px;height:58px;border-radius:18px;border:0;background:{{ $accent }};box-shadow:0 12px 34px -6px rgba({{$accentRgb}},0.55);cursor:pointer;display:flex;align-items:center;justify-content:center;pointer-events:auto;z-index:10;transition:transform .18s, box-shadow .18s;">
        <svg id="ai-chat-icon" style="width:26px;height:26px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
    </button>
</div>

<style>
    /* ============ DARK MODE (le shell pose .dark sur <html>) ============ */
    .dark #ai-chat-window { background:#0f172a !important; border-color:rgba(51,65,85,0.65) !important; box-shadow:0 24px 70px -12px rgba(0,0,0,0.7) !important; }
    .dark #ai-chat-window > div:first-child { background:#0f172a !important; border-bottom-color:rgba(51,65,85,0.55) !important; }
    .dark .ai-avatar { filter:brightness(1.05) !important; }
    .dark #ai-chat-window h1, .dark #ai-chat-window div { color-scheme:dark; }
    .dark .ai-window-header-title { color:#f1f5f9 !important; }
    .dark .ai-bubble-ai  { background:#1e293b !important; color:#cbd5e1 !important; border-color:rgba(51,65,85,0.5) !important; }
    .dark .ai-bubble-user{ color:#fff !important; }
    .dark .ai-inputbar   { background:#0f172a !important; border-top-color:rgba(51,65,85,0.55) !important; }
    .dark #ai-chat-input { background:#1e293b !important; color:#e2e8f0 !important; border-color:rgba(71,85,105,0.8) !important; }
    .dark .ai-sug        { background:rgba(129,140,248,0.10) !important; color:#a5b4fc !important; border-color:rgba(129,140,248,0.35) !important; }
    .dark .ai-sug:hover  { background:rgba(129,140,248,0.2) !important; }
    .dark .ai-fab-scroll { background:#1e293b !important; color:#a5b4fc !important; }
    .dark .ai-copy-btn   { background:#1e293b !important; color:#94a3b8 !important; }
    .dark .ai-copy-btn:hover { background:#334155 !important; color:#e2e8f0 !important; }
    .dark .ai-table th   { background:rgba(99,102,241,0.22) !important; color:#c7d2fe !important; }
    .dark .ai-table td   { border-color:rgba(71,85,105,0.7) !important; color:#cbd5e1 !important; }
    .dark #ai-sessions-drawer { background:#0f172a !important; }
    .dark #ai-chat-messages   { background:#0b1220 !important; }
    .dark .ai-status-dot { background:#10b981 !important; }
    .dark .ai-iconbtn:hover { background:rgba(51,65,85,0.8) !important; color:#e2e8f0 !important; }
    .dark .ai-session-row { background:#1e293b !important; }
    .dark .ai-session-row .ai-session-title { color:#e2e8f0 !important; }
    .dark .ai-session-row div { color:#94a3b8 !important; }  /* meta date lisible en dark */
    .dark .ai-session-row .ai-session-title { color:#e2e8f0 !important; }
    .dark .ai-msg-wrap { color:#cbd5e1; }

    /* ============ HEADER FIX (titres clairs en dark via hook classe) ============ */
    #ai-chat-window > div:first-child .ai-iconbtn { color:#475569; }
    #ai-chat-window > div:first-child > div:nth-child(2) > div:first-child { color:#0f172a; }
    .dark #ai-chat-window > div:first-child > div:nth-child(2) > div:first-child { color:#f1f5f9; }

    /* ============ Icônes header ============ */
    .ai-iconbtn { background:none;border:0;color:#64748b;cursor:pointer;padding:7px;border-radius:11px;display:flex;
        transition:background .15s, color .15s; }
    .ai-iconbtn:hover { background:rgba(148,163,184,0.18); color:#0f172a; }

    /* ============ FAB scroll ============ */
    .ai-fab-scroll { position:absolute;bottom:92px;right:16px;width:34px;height:34px;border-radius:12px;
        border:1px solid rgba(148,163,184,0.35);background:#fff;color:{{ $accent }};
        box-shadow:0 6px 18px rgba(15,23,42,0.18);cursor:pointer;display:none;align-items:center;justify-content:center;z-index:5;
        transition:transform .15s; }
    .ai-fab-scroll:hover { transform:translateY(-2px); }

    /* ============ Bulles 2026 ============ */
    .ai-bubble-ai, .ai-bubble-user {
        max-width:88%; padding:11px 15px; font-size:13.5px; line-height:1.6;
        border-radius:18px; word-wrap:break-word;
        unicode-bidi:plaintext;
    }
    /* les tableaux méritent plus de largeur : la bulle contenant une table passe à 92% via JS-free CSS */
    .ai-bubble-ai:has(.ai-table-wrap) { max-width:96%; }
    .ai-bubble-ai  { align-self:flex-start; background:#f1f5f9; color:#0f172a; border:1px solid rgba(148,163,184,0.28); border-bottom-left-radius:6px; }
    .ai-bubble-user{ align-self:flex-end; background:{{ $accent }}; color:#fff; border-bottom-right-radius:6px;
        white-space:pre-wrap; box-shadow:0 4px 14px -4px rgba({{$accentRgb}},0.45); }
    .dark .ai-bubble-ai { border-bottom-left-radius:6px !important; border-color:rgba(51,65,85,0.5) !important; }

    /* Status « en ligne » */
    .ai-status-dot { width:7px;height:7px;border-radius:50%;background:#10b981;display:inline-block;box-shadow:0 0 0 3px rgba(16,185,129,0.18); }

    /* ============ Chips ============ */
    .ai-sug { border:1.5px solid rgba(148,163,184,0.4); background:#fff; color:{{ $accent }};
        border-radius:14px; padding:7px 13px; font-size:12.5px; font-weight:600; cursor:pointer; font-family:inherit;
        transition:background .15s, border-color .15s, transform .15s, box-shadow .15s; }
    .ai-sug:hover { background:rgba({{$accentRgb}},0.08); border-color:rgba({{$accentRgb}},0.5); transform:translateY(-1px); box-shadow:0 4px 12px -4px rgba({{$accentRgb}},0.3); }

    /* ============ Copier ============ */
    .ai-msg-wrap { display:flex; flex-direction:column; max-width:86%; }
    .ai-msg-wrap.left { align-self:flex-start; }
    .ai-copy-btn { align-self:flex-start; margin-top:3px; border:0; background:transparent; color:#94a3b8;
        font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:4px; padding:3px 7px;
        border-radius:8px; opacity:0; transition:opacity .15s, background .15s; font-family:inherit; }
    .ai-msg-wrap:hover .ai-copy-btn { opacity:1; }
    .ai-copy-btn:hover { background:#f1f5f9; color:#475569; }

    /* ============ Tables ============ */
    /* Les tableaux de réponses financières ont besoin de largeur : max-width 100% de la
       bulle SANS min-width forcé (evitait l'étirement), wrap autorisé pour les libellés. */
    .ai-table-wrap { overflow-x:auto; max-width:100%; margin:8px 0 4px; border-radius:12px; border:1px solid rgba(148,163,184,0.25); }
    .ai-table { border-collapse:collapse; width:100%; min-width:0; font-size:12.5px; table-layout:auto; }
    .ai-table th, .ai-table td { border-bottom:1px solid rgba(148,163,184,0.25); padding:7px 10px; white-space:normal; word-break:keep-all; overflow-wrap:anywhere; border-right:0; border-left:0; vertical-align:top; }
    .ai-table th { background:rgba({{$accentRgb}},0.10); font-weight:700; color:{{ $accent }}; text-align:left; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
    .ai-table td { text-align:right; border-top:0; }
    .ai-table td:first-child { text-align:left; font-weight:600; white-space:nowrap; }
    /* les colonnes numériques restent nowrap pour aligner les montants */
    .ai-table td:not(:first-child) { white-space:nowrap; }
    .ai-table tr:last-child td { border-bottom:0; }
    .ai-table tr { transition:background .12s; }
    .ai-table tbody tr:nth-child(even) { background:rgba(148,163,184,0.05); }
    .dark .ai-table th { background:rgba(99,102,241,0.2) !important; text-transform:none; letter-spacing:0; }

    /* ============ RESPONS — mobile : PLEIN ÉCRAN ; desktop : fenêtre adaptée ============ */
    @media (max-width: 767px) {
        #ai-chat-window.ai-window.open {
            position: fixed !important;
            inset: 0 !important;
            bottom: 0 !important; left: 0 !important; right: 0 !important;
            width: 100vw !important; height: 100dvh !important; max-height: 100dvh !important;
            border-radius: 0 !important; border: 0 !important;
            box-shadow: none !important;
        }
        #ai-chatbot-toggle { bottom: 12px !important; }
        /* sur mobile le parent resize l'iframe lui-même (voir W/H dynamiques JS) */
    }
    @media (max-width: 767px) and (orientation: landscape) {
        #ai-chat-window.ai-window.open { height: 100dvh !important; }
    }
    /* Icônnes header plus grandes en tactile */
    @media (pointer: coarse) {
        .ai-iconbtn { padding: 9px; }
        #ai-chat-send { width: 48px; height: 48px; }
    }

    /* ============ Animations ============ */
    @keyframes aiPop { from { opacity:0; transform:translateY(16px) scale(0.93); } to { opacity:1; transform:none; } }
    .ai-window.open { display:flex !important; animation:aiPop .26s cubic-bezier(.2,.9,.3,1.15); }
    @keyframes aiBubbleIn { from { opacity:0; transform:translateY(7px) scale(0.98); } to { opacity:1; transform:none; } }
    .ai-bubble-ai, .ai-bubble-user, .ai-msg-wrap { animation:aiBubbleIn .2s ease-out; }
    #ai-chatbot-toggle:hover { transform:scale(1.06); box-shadow:0 16px 42px -6px rgba({{$accentRgb}},0.7); }
    #ai-chatbot-toggle:active { transform:scale(0.97); }
    @keyframes aiPulse { 0%,100% { box-shadow:0 0 0 0 rgba({{$accentRgb}},0.55); } 50% { box-shadow:0 0 0 12px rgba({{$accentRgb}},0); } }
    #ai-chatbot-toggle.pulse { animation:aiPulse 1.3s ease-out 6; }
    @keyframes aiBlink { 0%,80%,100% { opacity:.25 } 40% { opacity:1 } }

    /* ============ Focus input ring ============ */
    #ai-chat-input:focus { border-color:rgba({{$accentRgb}},0.6) !important; background:#fff !important;
        box-shadow:0 0 0 4px rgba({{$accentRgb}},0.10); }
    .dark #ai-chat-input:focus { background:#1e293b !important; }
    #ai-chat-send { width:44px;height:44px;border-radius:16px;border:0;background:{{ $accent }};color:#fff;cursor:pointer;
        display:flex;align-items:center;justify-content:center;flex-shrink:0;
        box-shadow:0 4px 14px -4px rgba({{$accentRgb}},0.5); transition:transform .15s, box-shadow .15s, opacity .15s; }
    #ai-chat-send:hover { transform:translateY(-1px) scale(1.04); }
    #ai-chat-send:disabled { opacity:0.45; cursor:wait; transform:none; }
    .dark .ai-fab-scroll { background:#1e293b !important; color:#a5b4fc !important; }

    /* ============ Scrollbar ============ */
    .ai-scroll::-webkit-scrollbar { width:5px; }
    .ai-scroll::-webkit-scrollbar-thumb { background:rgba(148,163,184,0.4); border-radius:3px; }
    .ai-scroll::-webkit-scrollbar-thumb:hover { background:rgba(148,163,184,0.65); }
    .ai-scroll::-webkit-scrollbar-track { background:transparent; }
    .dark .ai-scroll::-webkit-scrollbar-thumb { background:rgba(71,85,105,0.6); }
    .ai-scroll { scrollbar-width:thin; scrollbar-color:rgba(148,163,184,0.4) transparent; }
    .ai-dot { width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:aiBlink 1.2s infinite; }
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
        b.setAttribute('dir', 'auto'); // arabe = RTL automatique, fr/en = LTR
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
            // FIX : masque le FAB pendant que la fenêtre est ouverte (il chevauche l'input)
            toggle.style.opacity = '0';
            toggle.style.pointerEvents = 'none';
            unreadPending = false;
            setTimeout(function () { input.focus(); }, 120);
        } else {
            win.classList.remove('open');
            toggle.style.opacity = '1';
            toggle.style.pointerEvents = 'auto';
        }
        // Agrandit/reduit l iframe parent pour couvrir le chat (le bouton seul = 100x100)
        try {
            // FIX mobile : annonce aussi le mode plein écran au parent (taille d'iframe 100vw/100dvh)
            var isSmall = window.matchMedia('(max-width: 767px)').matches;
            parent.postMessage({ aiChatbot: v ? 'open' : 'close', mobile: isSmall }, '*');
        } catch (e) {}
    }

    toggle.addEventListener('click', function () { setOpen(!open); });
    closeBtn.addEventListener('click', function () { setOpen(false); });

    // Ouverture automatique demandee par le layout (setting ai_auto_open)
    // + SYNC DARK MODE : le parent pousse {aiChatbot:'theme', dark:bool} à chaque bascule
    // (l'iframe a son propre localStorage — sans ce pont, le widget reste clair pendant
    // que la platform est sombre).
    window.addEventListener('message', function (e) {
        // Securite : n'accepter les ordres que de notre propre origine (same-origin iframe)
        if (e.origin !== window.location.origin) return;
        var d = e.data || {};
        if (d.aiChatbot === 'openAuto' && !open) setOpen(true);
        if (d.aiChatbot === 'theme') {
            var isDark = !!d.dark;
            document.documentElement.classList.toggle('dark', isDark);
            try { localStorage.theme = isDark ? 'dark' : 'light'; } catch (er) {}
        }
    });

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
        // FIX UX#1 : bloque le double envoi / la redefinition pendant le streaming en cours
        if (typeof window.__aiStreaming !== 'undefined' && window.__aiStreaming) return;
        hideSuggestions();
        addBubble(msg, 'user');
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;
        var th = thinkingEl();
        var controller = new AbortController();
        var killed = false;
        // Bouton STOP pendant le streaming : le bouton envoyer devient ⏹ (abort à la demande, comme ChatGPT)
        var stopMode = false;
        window.__aiStreaming = true;  // FIX UX#1 : flag global "un stream tourne"
        function setStopMode(on) {
            var si = document.getElementById('ai-stop-icon');
            var ni = document.getElementById('ai-send-icon');
            if (si && ni) { si.style.display = on ? 'block' : 'none'; ni.style.display = on ? 'none' : 'block'; }
            sendBtn.disabled = !on;
            sendBtn.title = on ? '{{ __('ai.stop_generation') }}' : '';
        }
        function doStop() { if (!killed && stopMode) { killed = true; controller.abort(); finish(); } }
        sendBtn.addEventListener('click', function (e) {
            if (window.__aiStreaming) { e.preventDefault(); e.stopPropagation(); if (typeof doStop === 'function') doStop(); }
        }, true);

        // ─── STREAMING (SSE) ─────────────────────────────────────────────
        // La réponse arrive chunk par chunk dans la bubble assistant — rendu
        // progressif. En cas d'échec (SSE dispo pas, ancien PWA cache, réseau),
        // on bascule automatiquement sur le fallback JSON classique.
        fetch('/api/chatbot?stream=1', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'text/event-stream' },
            credentials: 'same-origin',
            body: JSON.stringify({ message: msg }),
            signal: controller.signal
        })
        .then(function (r) {
            if (!r.ok || !(r.headers.get('content-type') || '').includes('event-stream')) {
                // Serveur non-SSE (proxy, PWA obsolète) → fallback JSON
                return fallbackJson(msg, th);
            }
            var bubble = null;
            var text = '';
            var reader = r.body.getReader();
            var decoder = new TextDecoder();
            var buf = '';
            var everDelta = false;
            setStopMode(true); // streaming en cours → bouton = STOP
            stopMode = true;

            function process() {
                return reader.read().then(function (res) {
                    if (res.done) { finish(); return; }
                    buf += decoder.decode(res.value, { stream: true });
                    var idx;
                    while ((idx = buf.indexOf('\n\n')) !== -1) {
                        var block = buf.slice(0, idx);
                        buf = buf.slice(idx + 2);
                        var evt = 'delta', data = '';
                        block.split('\n').forEach(function (ln) {
                            if (ln.startsWith('event: ')) evt = ln.slice(7).trim();
                            else if (ln.startsWith('data: ')) data += ln.slice(6);
                        });
                        if (!data) continue;
                        try { data = JSON.parse(data); } catch (e) { continue; }
                        if (evt === 'delta' && data.text) {
                            everDelta = true;
                            if (!bubble) { th.remove(); var wrap = bubbleEl('', ''); bubble = wrap.querySelector('.ai-bubble-ai'); }
                            text += data.text;
                            // Rendu raw progressif (markdown finalisé à la fin)
                            bubble.innerHTML = md(text);
                            scrollBtn.style.display = box.scrollTop + box.clientHeight < box.scrollHeight - 60 ? 'flex' : 'none';
                        } else if (evt === 'error') {
                            everDelta = false;
                            th.remove();
                            addBubble(data.error || '{{ __('ai.api_error') }}', 'assistant');
                            killed = true; controller.abort(); finish(true); return;
                        } else if (evt === 'done') {
                            finish(true);
                            return;
                        }
                    }
                    return process();
                });
            }
            function finish(keep) {
                if (finished) return; finished = true;
                killed = true;
                th.remove();
                stopMode = false;
                window.__aiStreaming = false;  // FIX UX#1
                setStopMode(false); // retour au bouton envoyer
                if (bubble && text) {
                    // Rendu final : markdown + bouton copier, comme un bubble normal
                    bubble.innerHTML = md(text);
                }
                if (!open) { unreadPending = true; toggle.classList.add('pulse'); }
                sendBtn.disabled = false;
                scrollBtn.style.display = 'none';
            }
            var finished = false;
            return process().catch(function (e) {
                finish();
            });
        })
        .catch(function (e) {
            // Fetch stream cassé (réseau, proxy) — si AUCUN delta reçu → fallback JSON
            th.remove();
        });

        function fallbackJson(msg, th) {
            // FIX UX#3 : en fallback JSON il n'y a pas de streaming -> etat bouton propre
            window.__aiStreaming = false;
            var si = document.getElementById('ai-stop-icon'); var ni = document.getElementById('ai-send-icon');
            if (si && ni) { si.style.display = 'none'; ni.style.display = 'block'; }
            return fetch('/api/chatbot', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                credentials: 'same-origin',
                body: JSON.stringify({ message: msg }),
                signal: (function () { var c = new AbortController(); setTimeout(function () { c.abort(); }, 120000); return c.signal; })()
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
                sendBtn.disabled = false;
            })
            .catch(function () {
                th.remove();
                addBubble('{{ __('ai.api_error') }}', 'assistant');
            });
        }
    }

    clearBtn.addEventListener('click', function () {
        Swal.fire({
            title: @js(__('common.confirm')),
            text: @js(__('ai.clear_confirm')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: @js(__('common.confirm')),
            cancelButtonText: @js(__('common.cancel')),
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
            customClass: {
                popup: 'rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        }).then(function (result) {
            if (!result.isConfirmed) return;
        while (box.children.length > 1) box.removeChild(box.lastChild);
        hideSuggestions();
        // Recree les chips apres effacement — MEMES chips que le chargement initial
        // (custom si configurees dans /settings, sinon defaut) : source unique ci-dessous.
        var chipList = @js($chipsForJs);
        var showChips = @js(!empty($cfg['showSuggestions']));
        if (showChips) {
            var div = document.createElement('div');
            div.id = 'ai-chat-suggestions';
            div.style.cssText = 'display:flex;flex-wrap:wrap;gap:6px;';
            for (var ci = 0; ci < chipList.length; ci++) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'ai-sug';
                b.setAttribute('data-msg', chipList[ci]);
                b.textContent = chipList[ci];
                div.appendChild(b);
            }
            box.appendChild(div);
            sugs = div;
            bindSugs();
        }
        fetch('/api/chatbot/clear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            credentials: 'same-origin'
        });
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
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (window.__aiStreaming) { return; }  // FIX UX#1/4 : Enter pendant le streaming = ignoré (STOP via le bouton)
            send();
        }
    });

    // Historique serveur : rejoue les bulles au chargement; chips masquees si conversation existante
    var serverHistory = @json($chatHistory ?? []);
    if (serverHistory.length > 0) {
        hideSuggestions();
        serverHistory.forEach(function (m) {
            if (m && m.content) addBubble(m.content, m.role === 'user' ? 'user' : 'assistant');
        });
    }

    /* ==================== SESSIONS ==================== */
    var drawer = document.getElementById('ai-sessions-drawer');
    var sessionsBtn = document.getElementById('ai-chat-sessions');
    var sessionsBack = document.getElementById('ai-sessions-back');
    var sessionsNew = document.getElementById('ai-sessions-new');
    var sessionsList = document.getElementById('ai-sessions-list');
    var mainArea = document.getElementById('ai-chat-messages').parentElement;
    var MAIN_DISPLAY = '';

    function openDrawer() {
        MAIN_DISPLAY = mainArea.style.display;
        mainArea.style.display = 'none';
        var inputbar = document.querySelector('.ai-inputbar');
        inputbar.style.display = 'none';
        drawer.style.display = 'flex';
        loadSessions();
    }
    function closeDrawer() {
        drawer.style.display = 'none';
        mainArea.style.display = MAIN_DISPLAY || 'block';
        document.querySelector('.ai-inputbar').style.display = 'block';
    }
    function loadSessions() {
        sessionsList.innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:12px;padding:16px;">…</div>';
        fetch('/api/chatbot/sessions', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                sessionsList.innerHTML = '';
                var list = data.sessions || [];
                var activeId = data.activeId;
                if (!list.length) {
                    sessionsList.innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:12px;padding:24px;">{{ __('ai.no_sessions') }}</div>';
                    return;
                }
                list.forEach(function (s) {
                    var row = document.createElement('div');
                    row.className = 'ai-session-row';
                    var isActive = (s.id === activeId);
                    row.style.cssText = 'display:flex;align-items:center;gap:8px;padding:11px 13px;border-radius:14px;margin-bottom:7px;cursor:pointer;border:1.5px solid ' + (isActive ? 'rgba({{$accentRgb}},0.5)' : 'rgba(148,163,184,0.3)') + ';background:' + (isActive ? 'rgba({{$accentRgb}},0.07)' : '#fff') + ';';
                    var main = document.createElement('div');
                    main.style.cssText = 'flex:1;min-width:0;cursor:pointer;';
                    var title = document.createElement('div');
                    title.className = 'ai-session-title';
                    title.textContent = s.title || '{{ __('ai.title') }}';
                    title.style.cssText = 'font-size:13px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
                    var meta = document.createElement('div');
                    meta.textContent = new Date(s.last_activity).toLocaleString();
                    meta.style.cssText = 'font-size:11px;color:#94a3b8;margin-top:2px;';
                    main.appendChild(title); main.appendChild(meta);
                    main.addEventListener('click', function () { openSession(s.id, title.textContent); });
                    var del = document.createElement('button');
                    del.type = 'button';
                    del.title = '{{ __('ai.delete_session') }}';
                    del.innerHTML = '<svg style="width:15px;height:15px;color:#f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>';
                    del.style.cssText = 'background:none;border:0;cursor:pointer;padding:5px;border-radius:9px;color:#f87171;display:flex;transition:background .15s;';
                    del.addEventListener('mouseenter', function () { del.style.background = 'rgba(239,68,68,0.12)'; });
                    del.addEventListener('mouseleave', function () { del.style.background = 'none'; });
                    del.addEventListener('click', function (e) {
                        e.stopPropagation();
                        if (!confirm(@js(__('ai.delete_session_confirm')))) return;
                        fetch('/api/chatbot/sessions/' + s.id, { method: 'DELETE', credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                            .then(function () { loadSessions(); });
                    });
                    row.appendChild(main); row.appendChild(del);
                    sessionsList.appendChild(row);
                });
            });
    }
    function openSession(id, title) {
        fetch('/api/chatbot/sessions/' + id + '/open', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify({})
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.messages) {
                    // session vide ou erreur : affiche au moins un retour visible
                    addBubble(@js(__('ai.session_not_found')), 'assistant');
                    return;
                }
                while (box.children.length > 0) box.removeChild(box.lastChild);
                hideSuggestions();
                data.messages.forEach(function (m) {
                    if (m && m.content) addBubble(m.content, m.role === 'user' ? 'user' : 'assistant');
                });
                closeDrawer();
            })
            .catch(function (e) {
                addBubble(@js(__('ai.api_error')), 'assistant');
            });
    }
    if (sessionsBtn) sessionsBtn.addEventListener('click', openDrawer);
    if (sessionsBack) sessionsBack.addEventListener('click', closeDrawer);
    if (sessionsNew) sessionsNew.addEventListener('click', function () {
        fetch('/api/chatbot/new-session', { method: 'POST', credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function () {
                while (box.children.length > 0) box.removeChild(box.lastChild);
                // Show suggestions again for the fresh session
                var chipList = @js($chipsForJs);
                var showChips = @js(!empty($cfg['showSuggestions']));
                if (showChips && chipList.length) {
                    var div = document.createElement('div');
                    div.id = 'ai-chat-suggestions';
                    div.style.cssText = 'display:flex;flex-wrap:wrap;gap:6px;';
                    for (var ci = 0; ci < chipList.length; ci++) {
                        var b = document.createElement('button');
                        b.type = 'button'; b.className = 'ai-sug';
                        b.setAttribute('data-msg', chipList[ci]);
                        b.textContent = chipList[ci];
                        div.appendChild(b);
                    }
                    box.appendChild(div);
                    sugs = div;
                    bindSugs();
                }
                closeDrawer();
            });
    });
})();
</script>
