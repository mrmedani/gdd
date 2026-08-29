<?php

namespace App\Domains\Settings\Livewire;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Settings\Models\Setting;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Settings extends Component
{
    use WithPagination, WithToast, \Livewire\WithFileUploads;

    public float $threshold = 5000;
    public string $currency = 'MAD';
    public string $passwordCurrent = '';
    public string $passwordNew = '';
    public string $passwordConfirm = '';
    public string $locale = 'ar';
    public string $defaultLocale = 'ar';
    public string $defaultTheme = 'system';
    public int $unreadAlerts = 0;
    
    public $logo;
    public $favicon;
    public $pwaIcon;

    public string $appName = '';
    public string $pwaShortName = '';
    public string $pwaDescription = '';
    public string $pwaThemeColor = '#2563eb';
    public string $pwaThemeColorDark = '#0f172a';
    public string $pwaBgColor = '#f1f5f9';
    public string $pwaDisplay = 'standalone';
    public string $pwaOrientation = 'portrait-primary';

    public int $monthPeriodStartDay = 20;

    public string $testEmail = '';
    public string $cashDeficit = '0';
    public array $alertPreferences = [];

    public bool $whatsappEnabled = false;
    public string $whatsappChatId = '';
    public string $whatsappWorkerUrl = '';
    public bool $whatsappManualDisconnect = false;
    public int $whatsappMessageDelay = 5;
    public bool $loginPopupEnabled = false;
    public string $loginPopupContent = '';
    public ?string $waStatus = 'unknown';
    public ?string $waPhone = null;
    public ?string $waQr = null;
    public bool $waStarting = false;

    public string $geminiApiKey = '';
    public bool $geminiConfigured = false;
    public string $aiPersonality = '';

    // --- Personnalisation du widget IA (Categories 1 + 2) ---
    public string $aiName = '';
    public string $aiEmoji = '🤖';
    public string $aiGreeting = '';
    public string $aiSuggestions = '';
    public string $aiPalette = 'indigo';
    public string $aiPosition = 'right';
    public string $aiWindowSize = 'normal';
    public bool $aiAutoOpen = false;
    public bool $aiShowSuggestions = true;
    public bool $aiWidgetEnabled = true;
    public string $aiOfflineMessage = '';

    // Onglet actif de la page /settings (persiste dans l'URL : /settings?tab=ai)
    #[Url]
    public string $tab = 'general';

    public function mount(): void
    {
        Gate::authorize('manage-settings');

        $this->threshold = (float) Setting::get('high_expense_threshold', 5000);
        $this->currency = Setting::get('currency', 'MAD');
        $this->locale = auth()->user()->locale;
        $this->defaultLocale = Setting::get('default_locale', 'ar');
        $this->defaultTheme = Setting::get('default_theme', 'system');
        $this->unreadAlerts = Alert::unread()->count();
        $this->appName = Setting::get('app_name', config('app.name'));
        $this->pwaShortName = Setting::get('pwa_short_name', 'Chronorex');
        $this->pwaDescription = Setting::get('pwa_description', 'Application de gestion des dÃ©penses et trÃ©sorerie');
        $this->pwaThemeColor = Setting::get('pwa_theme_color', '#2563eb');
        $this->pwaThemeColorDark = Setting::get('pwa_theme_color_dark', '#0f172a');
        $this->pwaBgColor = Setting::get('pwa_bg_color', '#f1f5f9');
        $this->pwaDisplay = Setting::get('pwa_display', 'standalone');
        $this->pwaOrientation = Setting::get('pwa_orientation', 'portrait-primary');
        $this->monthPeriodStartDay = (int) Setting::get('month_period_start_day', 20);
        $this->cashDeficit = Setting::get('cash_deficit', '0');
        $this->alertPreferences = auth()->user()->alert_preferences ?? [];
        $this->whatsappEnabled = (bool) Setting::get('whatsapp_enabled', false);
        $this->whatsappChatId = Setting::get('whatsapp_chat_id', '');
        $this->whatsappWorkerUrl = Setting::get('whatsapp_worker_url', '/wa');
        $this->whatsappMessageDelay = (int) Setting::get('whatsapp_message_delay', 5);
        $this->loginPopupEnabled = (bool) Setting::get('login_popup_enabled', false);
        $this->loginPopupContent = Setting::get('login_popup_content', '');
        $this->pollWhatsAppStatus();
        $this->geminiApiKey = (string) Setting::get('gemini_api_key', '');
        $this->geminiConfigured = !empty($this->geminiApiKey);
        $this->aiPersonality = (string) Setting::get('ai_personality', '');
        $this->aiName = (string) Setting::get('ai_name', '');
        $this->aiEmoji = (string) Setting::get('ai_emoji', '🤖');
        $this->aiGreeting = (string) Setting::get('ai_greeting', '');
        $this->aiSuggestions = (string) Setting::get('ai_suggestions', '');
        $this->aiPalette = (string) Setting::get('ai_palette', 'indigo');
        $this->aiPosition = (string) Setting::get('ai_position', 'right');
        $this->aiWindowSize = (string) Setting::get('ai_window_size', 'normal');
        $this->aiAutoOpen = (bool) Setting::get('ai_auto_open', false);
        $this->aiShowSuggestions = (bool) Setting::get('ai_show_suggestions', true);
        $this->aiWidgetEnabled = (bool) Setting::get('ai_widget_enabled', true);
        $this->aiOfflineMessage = (string) Setting::get('ai_offline_message', '');
    }

    public function updateThreshold(): void
    {
        $this->validate(['threshold' => 'required|numeric|min:100']);

        Setting::set('high_expense_threshold', $this->threshold);

        $this->notify(__('common.saved'));
        $this->redirect(route('settings.index'), navigate: false);
    }

    public function updatePassword(): void
    {
        $key = 'password-update:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return;
        }

        $this->validate([
            'passwordCurrent' => 'required|current_password',
            'passwordNew' => 'required|min:8|same:passwordConfirm',
        ]);

        auth()->user()->update(['password' => bcrypt($this->passwordNew)]);
        $this->reset(['passwordCurrent', 'passwordNew', 'passwordConfirm']);
        RateLimiter::clear($key);
        $this->notify(__('common.saved'));

    }

    public function updateCurrency(): void
    {
        $this->validate(['currency' => 'required|string|max:10']);

        Setting::set('currency', $this->currency);

        $this->notify(__('common.saved'));
        $this->redirect(route('settings.index'), navigate: false);
    }

    public function updateLogo(): void
    {
        $this->validate(['logo' => 'required|image|max:2048']); // 2MB Max

        $path = $this->logo->store('settings', 'public');
        Setting::set('app_logo', $path);

        $this->notify(__('common.saved'));
        $this->redirect(route('settings.index'), navigate: false);
    }

    public function updateFavicon(): void
    {
        $this->validate(['favicon' => 'required|file|mimes:ico,png,jpg,jpeg|max:1024']); // 1MB Max

        $path = $this->favicon->store('settings', 'public');
        Setting::set('app_favicon', $path);

        $this->notify(__('common.saved'));
        $this->redirect(route('settings.index'), navigate: false);
    }

    public function updateAppName(): void
    {
        $this->validate(['appName' => 'required|string|max:255']);

        Setting::set('app_name', $this->appName);

        $this->notify(__('common.saved'));
        $this->redirect(route('settings.index'), navigate: false);
    }

    public function updateMonthPeriod(): void
    {
        $this->validate(['monthPeriodStartDay' => 'required|integer|min:1|max:28']);

        Setting::set('month_period_start_day', $this->monthPeriodStartDay);
        $this->notify(__('common.saved'));

    }

    public function updateCashDeficit(): void
    {
        $this->validate(['cashDeficit' => 'required|numeric|min:0']);

        Setting::set('cash_deficit', (string) $this->cashDeficit);
        $this->notify(__('common.saved'));

    }

    public function updatePwaSettings(): void
    {
        $this->validate([
            'pwaShortName' => 'required|string|max:30',
            'pwaDescription' => 'nullable|string|max:300',
            'pwaThemeColor' => 'required|string|max:7',
            'pwaThemeColorDark' => 'required|string|max:7',
            'pwaBgColor' => 'required|string|max:7',
            'pwaDisplay' => 'required|in:standalone,fullscreen,minimal-ui,browser',
            'pwaOrientation' => 'required|in:portrait-primary,portrait-secondary,landscape-primary,landscape-secondary,any',
        ]);

        Setting::set('pwa_short_name', $this->pwaShortName);
        Setting::set('pwa_description', $this->pwaDescription);
        Setting::set('pwa_theme_color', $this->pwaThemeColor);
        Setting::set('pwa_theme_color_dark', $this->pwaThemeColorDark);
        Setting::set('pwa_bg_color', $this->pwaBgColor);
        Setting::set('pwa_display', $this->pwaDisplay);
        Setting::set('pwa_orientation', $this->pwaOrientation);

        if ($this->pwaIcon) {
            $this->validate(['pwaIcon' => 'required|image|max:2048']);
            $path = $this->pwaIcon->store('settings', 'public');
            Setting::set('pwa_icon', $path);
        }

        $this->notify(__('common.saved'));
    }

    public function updateLocale(): void
    {
        auth()->user()->update(['locale' => $this->locale]);
        session(['locale' => $this->locale]);
        $this->notify(__('common.saved'));
        $this->redirect(route('settings.index'), navigate: false);
    }

    public function updateDefaultLocale(): void
    {
        $this->validate(['defaultLocale' => 'required|in:ar,fr,en']);
        Setting::set('default_locale', $this->defaultLocale);
        $this->notify(__('common.saved'));
    }

    public function updateDefaultTheme(): void
    {
        $this->validate(['defaultTheme' => 'required|in:light,dark,system']);
        Setting::set('default_theme', $this->defaultTheme);
        $this->notify(__('common.saved'));
    }

    public function markAlertsRead(): void
    {
        $query = Alert::unread();
        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }
        $query->update(['is_read' => true, 'read_at' => now()]);
        $this->unreadAlerts = 0;
    }

    public function deleteAlert(int $id): void
    {
        $alert = Alert::findOrFail($id);
        $prefs = auth()->user()?->alert_preferences;
        if ($prefs && !in_array($alert->type, $prefs)) {
            abort(403);
        }
        $alert->delete();
        $query = Alert::unread();
        if ($prefs) {
            $query->whereIn('type', $prefs);
        }
        $this->unreadAlerts = $query->count();
        $this->notify(__('common.deleted'));
    }

    public function deleteAllData(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        \DB::transaction(function () {
            \App\Domains\Alerts\Models\Alert::query()->delete();
            \App\Domains\Employees\Models\SalaryAdvance::query()->delete();
            \App\Domains\Employees\Models\SalaryPayment::query()->delete();
            \App\Domains\Expenses\Models\Expense::query()->delete();
            \App\Domains\Employees\Models\Employee::query()->delete();
            \App\Domains\Treasury\Models\MonthlyClosure::query()->delete();
        });

        $this->notify(__('common.deleted'));
        $this->redirect(route('settings.index'), navigate: false);
    }

    public function runCronJob(string $command): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $allowed = [
            'alerts:high-expenses',
            'alerts:salary-reminders',
            'alerts:missing-receipts',
            'alerts:check-budgets',
            'backup:database',
            'cache:clear',
        ];

        if (!in_array($command, $allowed)) {
            return;
        }

        try {
            Artisan::call($command);
            $this->notify(__('common.saved'));
        } catch (\Throwable $e) {
        }
    }
    public function updateWhatsAppConfig(): void
    {
        $this->validate([
            'whatsappEnabled' => 'boolean',
            'whatsappChatId' => 'nullable|string|max:255',
            'whatsappWorkerUrl' => 'nullable|string|max:255',
        ]);

        Setting::set('whatsapp_enabled', $this->whatsappEnabled ? '1' : '0');
        Setting::set('whatsapp_chat_id', $this->whatsappChatId);
        Setting::set('whatsapp_worker_url', $this->whatsappWorkerUrl ?: 'http://127.0.0.1:9090');

        $this->notify(__('common.saved'));
    }

    public function updateWhatsAppMessageDelay(): void
    {
        $this->validate(['whatsappMessageDelay' => 'required|integer|min:1|max:30']);
        Setting::set('whatsapp_message_delay', (string) $this->whatsappMessageDelay);
        $this->notify(__('common.saved'));
    }

    public function disconnectWhatsApp(): void
    {
        $url = $this->whatsappWorkerUrl ?: 'http://127.0.0.1:9090';
        WhatsAppService::disconnect($url);
        $this->whatsappManualDisconnect = true;
        $this->waStatus = 'disconnected';
        $this->waPhone = null;
        $this->waQr = null;
        $this->waStarting = false;
        $this->notify(__('settings.whatsapp_disconnected_alert'));
    }

    public function startWhatsAppWorker(): void
    {
        $url = $this->whatsappWorkerUrl ?: 'http://127.0.0.1:9090';
        WhatsAppService::startWorker($url);
        $this->whatsappManualDisconnect = false;
        $this->waStarting = true;
        $this->waStatus = 'starting';
        $this->waQr = null;
        $this->notify(__('settings.whatsapp_worker_started'));
    }

    public function updateLoginPopup(): void
    {
        $this->validate([
            'loginPopupEnabled' => 'boolean',
            'loginPopupContent' => 'nullable|string|max:5000',
        ]);

        Setting::set('login_popup_enabled', $this->loginPopupEnabled ? '1' : '0');
        Setting::set('login_popup_content', $this->loginPopupContent);

        $this->notify(__('common.saved'));
    }

    public function updateGeminiKey(): void
    {
        $this->validate(['geminiApiKey' => 'required|string|min:10|max:255']);

        Setting::set('gemini_api_key', $this->geminiApiKey);
        $this->geminiConfigured = true;

        $this->notify(__('settings.gemini_saved'));
    }

    public function updateAiPersonality(): void
    {
        $this->validate(['aiPersonality' => 'nullable|string|max:4000']);

        // Vide = retour au prompt par defaut (le controller a le fallback)
        Setting::set('ai_personality', trim($this->aiPersonality));
        $this->notify(__('settings.ai_personality_saved'));
    }

    public function resetAiPersonality(): void
    {
        Setting::set('ai_personality', '');
        $this->aiPersonality = '';
        $this->notify(__('settings.ai_personality_reset'));
    }

    public function updateAiIdentity(): void
    {
        $this->validate([
            'aiName'         => 'nullable|string|max:30',
            'aiEmoji'        => 'nullable|string|max:8',
            'aiGreeting'     => 'nullable|string|max:500',
            'aiSuggestions'  => 'nullable|string|max:2000',
        ]);

        // Nom : vide = extraction automatique depuis le prompt de personnalite
        Setting::set('ai_name', trim($this->aiName));
        Setting::set('ai_emoji', trim($this->aiEmoji) ?: '🤖');
        Setting::set('ai_greeting', trim($this->aiGreeting));
        // Chips : 1 question par ligne, vide = defaut
        $sugs = collect(explode("\n", str_replace("\r", '', $this->aiSuggestions)))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->take(6)
            ->implode("\n");
        Setting::set('ai_suggestions', $sugs);

        $this->notify(__('settings.ai_identity_saved'));
    }

    public function updateAiAppearance(): void
    {
        $this->validate([
            'aiPalette'       => 'required|in:indigo,emerald,ocean,sunset,slate,rose',
            'aiPosition'      => 'required|in:right,left',
            'aiWindowSize'    => 'required|in:normal,large',
            'aiAutoOpen'      => 'boolean',
            'aiShowSuggestions' => 'boolean',
            'aiWidgetEnabled' => 'boolean',
            'aiOfflineMessage' => 'nullable|string|max:300',
        ]);

        Setting::set('ai_palette', $this->aiPalette);
        Setting::set('ai_position', $this->aiPosition);
        Setting::set('ai_window_size', $this->aiWindowSize);
        Setting::set('ai_auto_open', $this->aiAutoOpen ? '1' : '0');
        Setting::set('ai_show_suggestions', $this->aiShowSuggestions ? '1' : '0');
        Setting::set('ai_widget_enabled', $this->aiWidgetEnabled ? '1' : '0');
        Setting::set('ai_offline_message', trim($this->aiOfflineMessage));

        $this->notify(__('settings.ai_appearance_saved'));
    }

    public function pollWhatsAppStatus(): void
    {
        $url = $this->waWorkerUrl();
        $status = WhatsAppService::getStatus($url);
        $statusData = $status ?: ['status' => 'unknown', 'phone' => null];

        $this->waStatus = $statusData['status'] ?? 'unknown';
        $this->waPhone = $statusData['phone'] ?? null;

        if ($this->waStatus === 'starting') {
            $this->waStarting = true;
        } elseif ($this->waStarting) {
            $this->waStarting = false;
        }

        if ($this->waStatus === 'qr_ready' && !$this->waQr) {
            $qr = WhatsAppService::getQr($url);
            if ($qr) {
                $this->waQr = $qr;
            }
        } elseif ($this->waStatus !== 'qr_ready') {
            $this->waQr = null;
        }

        if ($this->waStatus === 'connected' && $this->waPhone && !$this->whatsappEnabled) {
            $this->whatsappChatId = $this->waPhone;
            $this->whatsappEnabled = true;
            $this->updateWhatsAppConfig();
        }
    }

    public function refreshQr(): void
    {
        $url = $this->waWorkerUrl();
        WhatsAppService::startWorker($url);
        $this->waQr = null;
        $this->waStarting = true;
        $this->waStatus = 'starting';
        $this->whatsappManualDisconnect = false;
    }

    private function waWorkerUrl(): string
    {
        $url = $this->whatsappWorkerUrl;
        if (empty($url) || $url === '/wa') {
            return 'http://127.0.0.1:9090';
        }
        return $url;
    }

    public function getWhatsAppQr(): ?string
    {
        $url = $this->waWorkerUrl();
        return WhatsAppService::getQr($url);
    }

    #[Computed]
    public function whatsappStatus(): ?string
    {
        $url = $this->waWorkerUrl();
        $status = WhatsAppService::getStatus($url);
        return $status['status'] ?? 'unknown';
    }

    public function fetchWhatsAppData(): array
    {
        $url = $this->waWorkerUrl();
        $status = WhatsAppService::getStatus($url);
        $statusData = $status ?: ['status' => 'unknown', 'phone' => null];
        $qr = WhatsAppService::getQr($url);
        return [
            'status' => $statusData['status'] ?? 'unknown',
            'phone' => $statusData['phone'] ?? null,
            'qr' => $qr,
        ];
    }

    public function getAllAlertTypesProperty(): array
    {
        return Alert::select('type')->distinct()->pluck('type')->toArray();
    }

    public function updateAlertPreferences(): void
    {
        auth()->user()->update(['alert_preferences' => $this->alertPreferences]);
        $this->notify(__('common.saved'));
    }

    public function clearCache(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        try {
            Artisan::call('optimize:clear');
            $this->notify(__('settings.cache_cleared'));
            $this->dispatch('cache-cleared');
        } catch (\Throwable $e) {
        }
    }

    public function render()
    {
        $query = Alert::latest();
        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }
        $alerts = $query->paginate(10, pageName: 'alerts_page');

        return view('livewire.settings', compact('alerts'))
            ->layout('layouts.app')
            ->title(__('nav.settings'));
    }
}
