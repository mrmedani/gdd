<?php

namespace App\Domains\Expenses\Livewire;

use App\Domains\Expenses\Exports\ExpenseFilteredExport;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Shared\Enums\PaymentMethod; // FIX #1 : import manquant → crash « Class not found » à l'import CSV
use Maatwebsite\Excel\Facades\Excel;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ExpenseList extends Component
{
    use WithToast;
    use WithPagination;
    use WithFileUploads;

    public string $searchDateFrom = '';
    public string $searchDateTo = '';
    public string $searchCategory = '';
    public string $searchAmountMin = '';
    public string $searchAmountMax = '';
    public string $searchKeyword = '';
    public string $searchCreator = ''; // FIX #9 : filtre admin « créé par »

    // Modal de détail (FIX #11 + #12)
    public bool $showExpenseModal = false;
    public array $expenseDetails = [];

    // Bulk actions
    public array $selectedExpenses = [];
    public bool $selectAll = false;

    // Import
    public bool $showImportModal = false;
    public bool $showImportPreview = false;
    public $importFile = null;
    public array $importPreviewRows = [];
    public string $importLog = '';

    protected $queryString = [
        'searchDateFrom' => ['except' => ''],
        'searchDateTo' => ['except' => ''],
        'searchCategory' => ['except' => ''],
        'searchAmountMin' => ['except' => ''],
        'searchAmountMax' => ['except' => ''],
        'searchKeyword' => ['except' => ''],
        'searchCreator' => ['except' => ''],
    ];

    /**
     * FIX : le filtre de date démarre SUR LA PÉRIODE COMPTABLE COURANTE (21→20)
     * si l'URL n'a aucun filtre de date. L'utilisateur peut librement éditer
     * les 2 dates — les queryString continuent de les conserver.
     */
    public function mount(): void
    {
        if ($this->searchDateFrom === '' && $this->searchDateTo === '') {
            $range = getPeriodRange(getPeriodFromDate(now()));
            $this->searchDateFrom = $range['start']->format('Y-m-d');
            $this->searchDateTo = $range['end']->format('Y-m-d');
        }
    }

    /** Bouton rapide : revenir à la période courante (21→20). */
    public function applyCurrentPeriod(): void
    {
        $range = getPeriodRange(getPeriodFromDate(now()));
        $this->searchDateFrom = $range['start']->format('Y-m-d');
        $this->searchDateTo = $range['end']->format('Y-m-d');
        $this->resetPage();
    }

    public function updated(string $property, mixed $value): void
    {
        if (str_starts_with($property, 'search')) {  // searchCreator compris
            $this->resetPage();
        }
    }

    public function delete(int $id): void
    {
        $expense = Expense::findOrFail($id);
        Gate::authorize('delete', $expense);

        try {
            Expense::assertPeriodNotClosed($expense->date);
        } catch (\Exception $e) {
            $this->notify($e->getMessage(), 'error');
            return;
        }

        $expense->delete();
        $this->notify(__('common.deleted'));
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedExpenses = $value
            ? $this->getFilteredQuery()->latest()->pluck('id')->toArray()
            : [];
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedExpenses)) return;

        Gate::authorize('delete', Expense::class);

        $expenses = Expense::whereIn('id', $this->selectedExpenses)->get();

        foreach ($expenses as $expense) {
            try {
                Expense::assertPeriodNotClosed($expense->date);
            } catch (\Exception $e) {
                $this->notify($e->getMessage(), 'error');
                return;
            }
        }

        Expense::whereIn('id', $this->selectedExpenses)->delete();
        $this->selectedExpenses = [];
        $this->selectAll = false;
        $this->notify(__('common.deleted'));
    }

    public function getFilteredQuery()
    {
        $query = Expense::with('category', 'creator', 'employee');

        if ($this->searchDateFrom) {
            $query->whereDate('date', '>=', $this->searchDateFrom);
        }
        if ($this->searchDateTo) {
            $query->whereDate('date', '<=', $this->searchDateTo);
        }
        if ($this->searchCategory) {
            $query->where('category_id', $this->searchCategory);
        }
        if ($this->searchAmountMin !== '') {
            $query->where('amount', '>=', (float) $this->searchAmountMin);
        }
        if ($this->searchAmountMax !== '') {
            $query->where('amount', '<=', (float) $this->searchAmountMax);
        }
        if ($this->searchKeyword) {
            $keyword = '%' . $this->searchKeyword . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('description', 'like', $keyword)
                  ->orWhere('notes', 'like', $keyword);
            });
        }

        // FIX #9 : admin peut filtrer par créateur
        if ($this->searchCreator !== '' && auth()->user()?->isAdmin()) {
            $query->where('created_by', (int) $this->searchCreator);
        }

        return $query;
    }

    /** FIX #7 : entêtes i18n (fr/ar/en) au lieu d'un texte anglais en dur. */
    public function exportCsv()
    {
        Gate::authorize('create', Expense::class);
        return Excel::download(new ExpenseFilteredExport($this->getFilteredQuery()->latest()->get()),
            'depenses-' . now()->format('Y-m-d-His') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /** FIX #7 : export Excel de la sélection courante (mêmes filtres que le tableau). */
    public function exportExcel()
    {
        Gate::authorize('create', Expense::class);
        return Excel::download(new ExpenseFilteredExport($this->getFilteredQuery()->latest()->get()),
            'depenses-' . now()->format('Y-m-d-His') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /** FIX #13 : exporter UNIQUEMENT les dépenses cochées. */
    public function exportSelected()
    {
        if (empty($this->selectedExpenses)) return;
        Gate::authorize('create', Expense::class);
        $rows = Expense::with('category', 'creator', 'employee')
            ->whereIn('id', $this->selectedExpenses)->latest()->get();
        return Excel::download(new ExpenseFilteredExport($rows),
            'depenses-selection-' . now()->format('Y-m-d-His') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function previewCsv(): void
    {
        Gate::authorize('create', Expense::class);

        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->importFile->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))), $header);

        $expected = ['date', 'description', 'amount'];
        $missing = array_diff($expected, $header);
        if (count($missing) > 0) {
            fclose($handle);
            // FIX #3 : on ne retourne plus SILENCIEUSEMENT — l'utilisateur voit pourquoi
            $this->notify(__('expenses.import_bad_header', ['columns' => implode(', ', $missing)]), 'error');
            return;
        }

        $this->importPreviewRows = [];
        $categoryCache = [];
        $rowNum = 0;

        while (($row = fgetcsv($handle)) !== false && $rowNum < 20) {
            $rowNum++;
            try {
                $data = array_combine($header, $row);
                $date = trim($data['date'] ?? '');
                $description = trim($data['description'] ?? '');
                $amount = str_replace(',', '.', trim($data['amount'] ?? '0'));

                if (empty($date) || empty($description) || (float) $amount <= 0) continue;

                $categoryKey = trim($data['category'] ?? 'other');
                if (!isset($categoryCache[$categoryKey])) {
                    $cat = ExpenseCategory::where('key', $categoryKey)->orWhere('name_fr', $categoryKey)->first();
                    $categoryCache[$categoryKey] = $cat?->id ?? ExpenseCategory::where('key', 'other')->first()?->id ?? 1;
                }

                $parsedDate = $this->parseImportDate($date);
                if ($parsedDate === null) {
                    continue; // FIX #4 : date non conforme → ligne sautée (pas de 0000-00 en base)
                }

                $this->importPreviewRows[] = [
                    'date' => $parsedDate->format('Y-m-d'),
                    'description' => $description,
                    'amount' => (float) $amount,
                    'category' => $categoryKey,
                    'duplicate' => $this->isDuplicateImport($parsedDate->toDateString(), (float) $amount, $description)
                        || collect($this->importPreviewRows)->contains(fn ($r) => $r['date'] === $parsedDate->format('Y-m-d')
                            && (float) $r['amount'] === (float) $amount
                            && $r['description'] === $description),
                ];
            } catch (\Throwable) {
                continue;
            }
        }

        fclose($handle);
        $this->showImportPreview = !empty($this->importPreviewRows);
    }

    /**
     * FIX #4 : parse une date CSV (Y-m-d / d/m/Y / d-m-Y / Y/m/d).
     * ⚠️ createFromFormat LANCE une InvalidFormatException plutôt que retourner false
     * (ex : '31/31/2026' et tout format incompatible) → try/catch à CHAQUE tentative,
     * sinon la ligne valide suivante sautée silencieusement.
     */
    private function parseImportDate(string $raw): ?\Carbon\Carbon
    {
        $raw = trim($raw);
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            try {
                $c = \Carbon\Carbon::createFromFormat($fmt, $raw);
                if ($c instanceof \Carbon\Carbon && $c->format($fmt) === $raw) {
                    return $c;
                }
            } catch (\Carbon\Exceptions\InvalidFormatException) {
                continue;
            } catch (\Throwable) {
                continue;
            }
        }
        return null;
    }

    /** FIX #8 : doublon = (date, montant, description) déjà en base. */
    private function isDuplicateImport(string $date, float $amount, string $description): bool
    {
        return Expense::whereDate('date', $date)
            ->where('amount', $amount)
            ->where('description', $description)
            ->exists();
    }

    public function confirmImport(): void
    {
        Gate::authorize('create', Expense::class);

        if (!is_file($this->importFile?->getRealPath() ?? '')) {
            $this->notify(__('expenses.import_no_file', ['default' => 'Aucun fichier importé.']), 'error');
            return;
        }

        $path = $this->importFile->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))), $header);

        $expected = ['date', 'description', 'amount'];
        if (count(array_diff($expected, $header)) > 0) {
            fclose($handle);
            $this->notify(__('expenses.import_bad_header', ['columns' => implode(', ', array_diff($expected, $header))]), 'error');
            return;
        }

        $imported = 0;
        $skippedDupes = 0;
        $errors = 0;
        $categoryCache = [];
        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);
                $description = trim($data['description'] ?? '');
                $amount = str_replace(',', '.', trim($data['amount'] ?? '0'));

                $parsedDate = $this->parseImportDate(trim($data['date'] ?? ''));
                if ($parsedDate === null || $description === '' || (float) $amount <= 0) {
                    $errors++;
                    continue;
                }

                // FIX #8 : doublons (date+montant+description) détectés et sautés.
                // ⚠️ Le check couvre la BASE **ET** les lignes déjà collectées du même
                // fichier (sinon le doublon intra-fichier passe à travers le lot).
                $isDupe = $this->isDuplicateImport($parsedDate->toDateString(), (float) $amount, $description)
                    || collect($rows)->contains(fn ($r) => $r['date'] === $parsedDate->toDateString()
                        && (float) $r['amount'] === (float) $amount
                        && $r['description'] === $description);
                if ($isDupe) {
                    $skippedDupes++;
                    continue;
                }

                $categoryKey = trim($data['category'] ?? 'other');
                if (!isset($categoryCache[$categoryKey])) {
                    // FIX #2 : fallback PROPRE (catégorie 'other') au lieu de l'id 1 en dur
                    $cat = ExpenseCategory::where('key', $categoryKey)->orWhere('name_fr', $categoryKey)->first();
                    $categoryCache[$categoryKey] = $cat?->id
                        ?? ExpenseCategory::where('key', 'other')->first()?->id
                        ?? null;
                    $categoryCache[$categoryKey . '::key'] = $cat?->key ?? 'other';
                }

                $paymentMethod = trim($data['payment_method'] ?? 'cash');
                $validMethods = array_column(PaymentMethod::cases(), 'value');
                if (!in_array($paymentMethod, $validMethods)) $paymentMethod = 'cash';

                $rows[] = [
                    'date' => $parsedDate->toDateString(),
                    'description' => $description,
                    'amount' => (float) $amount,
                    'category_id' => $categoryCache[$categoryKey],
                    'category_key' => $categoryCache[$categoryKey . '::key'],
                    'payment_method' => $paymentMethod,
                    'notes' => trim($data['notes'] ?? ''),
                    'created_by' => auth()->id(),
                ];
            } catch (\Throwable) {
                $errors++;
            }
        }

        fclose($handle);

        // FIX #5 : 1 SEULE notification à la fin (flag silencieux sur l'observateur),
        // PAS 20 messages WhatsApp/supérieur pour 20 lignes
        \App\Domains\Expenses\Observers\ExpenseObserver::$skipNotifications = true;
        try {
            DB::transaction(function () use ($rows, &$imported) {
                foreach ($rows as $row) {
                    Expense::create($row);
                    $imported++;
                }
            });
        } finally {
            \App\Domains\Expenses\Observers\ExpenseObserver::$skipNotifications = false;
        }

        $this->showImportModal = false;
        $this->showImportPreview = false;
        $this->importFile = null;
        $this->importPreviewRows = [];

        $this->notify(__('expenses.import_done', ['ok' => $imported, 'dupe' => $skippedDupes, 'err' => $errors]));
    }

    /** FIX #11 : détail d'une dépense en un clic (modal), sans naviguer. */
    public function viewExpense(int $id): void
    {
        $e = Expense::with('category', 'creator', 'employee')->find($id);
        if (!$e) return;

        $this->expenseDetails = [
            'id' => $e->id,
            'date' => $e->date->format('Y-m-d'),
            'description' => $e->description,
            'amount' => (float) $e->amount,
            'category' => $e->category?->translated_name ?? $e->category_key,
            'payment_method' => $e->payment_method,
            'employee' => $e->employee?->name ?? '',
            'notes' => $e->notes ?? '',
            'created_by' => $e->creator?->name ?? '-',
            'receipt_path' => $e->receipt_path ?? '',
            'is_image' => $this->isReceiptImage($e->receipt_path ?? ''),
        ];
        $this->showExpenseModal = true;
        $this->dispatch('open-expense-modal');
    }

    /** FIX #12 : reçu visionné DANS le modal (img directe vs lien PDF). */
    private function isReceiptImage(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
    }

    public function closeExpenseModal(): void
    {
        $this->showExpenseModal = false;
        $this->expenseDetails = [];
    }

    public function cancelImport(): void
    {
        $this->showImportPreview = false;
        $this->importPreviewRows = [];
        $this->importFile = null;
    }

    public function render()
    {
        $query = $this->getFilteredQuery();
        $expenses = (clone $query)->latest()->paginate(15);
        $categories = ExpenseCategory::active()->get();

        // FIX #6 : résumé du filtre — total & nb d'affichés
        $totalFiltered = (float) (clone $query)->sum('amount');
        $countFiltered = (clone $query)->count();

        // FIX #9 : options créateur (compatibilité admin uniquement)
        $creators = auth()->user()?->isAdmin()
            ? \App\Models\User::whereIn('id', Expense::select('created_by')->distinct()->limit(200)->pluck('created_by'))
                ->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('livewire.expense-list', [
            'expenses' => $expenses,
            'categories' => $categories,
            'creators' => $creators,
            'totalFiltered' => $totalFiltered,
            'countFiltered' => $countFiltered,
        ])->layout('layouts.app')->title(__('nav.expenses'));
    }
}
