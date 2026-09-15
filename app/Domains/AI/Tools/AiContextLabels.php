<?php

namespace App\Domains\AI\Tools;

/**
 * En-têtes et libellés du contexte IA, localisés (fr/ar/en).
 *
 * POURQUOI pas __() : les clés JSON vivent dans files/lang/*.json chargés PAR LE
 * FRONT-END ; côté serveur, le contexte IA est généré AVANT que app_locale soit
 * garantim fixé (ChatbotController::setLocale est bien branché mais des stub des
 * helpers getCurrency → string constants font error). Dictionnaire privé = zéro
 * dépendance, traduction garantie, pas de risque de clé manquante (fallback fr).
 *
 * Chaque entrée est un sprintf-safe format ; les placeholders sont remplis par
 * le contexte enfant (%s, %d).
 */
class AiContextLabels
{
    protected array $labels;

    public function __construct(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale() ?: 'fr';

        // ⚠ Mettre à jour : ajout d'une langue = étendre ces trois sections ici.
        $this->labels = match ($locale) {
            'ar' => [
                'summary' => 'الملخص التنفيذي (أرقام رسمية أُعيد حسابها الآن):',
                'today' => "تاريخ اليوم : %s (أسئلة «اليوم» تخصّ هذا التاريخ)",
                'exp_current' => "مصاريف الفترة الحالية (%s) :",
                'inc_current' => 'مداخيل الفترة الحالية :',
                'exp_all' => "المصاريف لجميع الفترات :",
                'inc_all' => 'المداخيل لجميع الفترات :',
                'daily_header' => 'المصاريف اليومية (آخر 10 أيام بالفترة الحالية):',
                'today_label' => 'اليوم',
                'no_daily' => 'لم تُسجل أي مصاريف بعد في الفترة الحالية.',
                'none_today' => 'لا مصاريف اليوم (0)',
                'period_current' => 'الفترة الحالية',
                'period' => 'الفترة',
                'exp_total' => 'مجموع المصاريف :',
                'inc_total' => 'مجموع المداخيل :',
                'net_gain' => 'الصافي (مداخيل - مصاريف) :',
                'by_category' => 'التفصيل الكامل حسب الفئة (فئة: المجموع على عدد المصاريف):',
                'recurring_in' => 'مصاريف متكررة داخل هذه الفترة:',
                'recurrent_header' => 'المصاريف المتكررة (≥3 فترات) مع الفترات الدقيقة:',
                'employees' => 'الموظفون (عدد الفريق الفعلي):',
                'closures' => 'الإقفالات الشهرية الرسمية:',
                'incomes' => 'مداخيل (تفصيل الفترات):',
                'coverage' => 'تغطية المعرفة: تعرف فقط على ما سبق. لا ترى: ملاحظات خاصة، بيانات محذوفة، كلمات سر، بيانات شركات أخرى، فترات أقدم من المذكور.',
                'hidden_warn' => 'مهم: دورك لا يمنح صلاحية الوصول إلى: %s. الرد «هذه المعلومة تحتاج صلاحية لا يمتلكها حسابك» — لا تُقَدِّر أبدًا.',
                'out_of_scope' => 'أي سؤال خارج التغطية: «هذه الدقة ليست في بياناتي».',
                'empty_period' => 'الفترة %s (من %s إلى %s): لا توجد بيانات مسجلة (لا مصاريف ولا مداخيل).',
                'none_on_period' => 'لا توجد بيانات مسجلة خلال هذه الفترة (0).',
                'module_contracts' => 'عقود: لا صلاحية لهذه الصفحة — النموذج لا يستطيع تعديلها.',
                'module_commitments' => 'الالتزامات (تنبيهات الفواتير): غير ضمن التعاقدات.',
                'module_investments' => 'الاستثمارات: غير مضمّنة.',
            ],
            'en' => [
                'summary' => 'EXECUTIVE SUMMARY (official figures recomputed just now):',
                'today' => "TODAY'S DATE : %s (questions about today refer to this date)",
                'exp_current' => 'Current period expenses (%s):',
                'inc_current' => 'Incomes current period:',
                'exp_all' => 'Expenses ALL periods:',
                'inc_all' => 'Incomes ALL periods:',
                'daily_header' => 'DAILY EXPENSES (last 10 active days in current period):',
                'today_label' => "TODAY",
                'no_daily' => 'No expenses recorded yet in current period.',
                'none_today' => 'No expenses today (0)',
                'period_current' => 'CURRENT period',
                'period' => 'Period',
                'exp_total' => 'Expenses TOTAL:',
                'inc_total' => "Incomes TOTAL:",
                'net_gain' => 'NET GAIN (incomes - expenses):',
                'by_category' => 'Full breakdown by category (category: total on N expenses):',
                'recurring_in' => 'RECURRING expenses paid in this period:',
                'recurrent_header' => 'RECURRING expenses (>= 3 distinct periods) with their EXACT periods:',
                'employees' => 'EMPLOYEES (actual headcount):',
                'closures' => 'MONTHLY CLOSURES (official):',
                'incomes_summary' => 'CASH INFLOWS (exhaustive active detail):',
                'coverage' => 'COVERAGE: you know ONLY what follows. You do NOT see: private notes, deleted data (trash), passwords, other companies, older periods than listed.',
                'hidden_warn' => "IMPORTANT: your role does NOT allow access to: %s. If asked, reply 'This information requires a permission your account does not have' — never estimate.",
                'out_of_scope' => 'For anything outside coverage: "this detail is not in my data".',
                'empty_period' => 'Period %s (from %s to %s): NO data recorded (neither expenses nor incomes).',
                'none_on_period' => 'No data recorded in this period (0).',
                'module_contracts' => 'Contracts: not included.',
                'module_commitments' => 'Monthly commitments: not included.',
                'module_investments' => 'Investments: not included.',
            ],
            default => [ // fr par défaut
                'summary' => "RÉSUMÉ EXÉCUTIF (chiffres officiels recalculés à l'instant) :",
                'today' => "DATE D'AUJOURD'HUI : %s (les questions « aujourd'hui » portent sur cette date)",
                'exp_current' => 'Dépenses période actuelle (%s) :',
                'inc_current' => 'Entrées période actuelle :',
                'exp_all' => 'Dépenses TOUTES PÉRIODES confondues :',
                'inc_all' => 'Entrées TOUTES PÉRIODES confondues :',
                'daily_header' => 'DÉPENSES PAR JOUR (10 derniers jours ayant des dépenses dans la période actuelle) :',
                'today_label' => "AUJOURD'HUI",
                'no_daily' => "Aucune dépense enregistrée à ce jour dans la période actuelle (\"aujourd'hui\" = 0 si aucune saisie).",
                'none_today' => "AUCUNE dépense enregistrée (0)",
                'period_current' => 'Période ACTUELLE',
                'period' => 'Période',
                'exp_total' => 'Dépenses TOTAL :',
                'inc_total' => "Entrées d'argent TOTAL :",
                'net_gain' => 'GAIN NET de la période (entrées - dépenses) :',
                'by_category' => 'Détail COMPLET par catégorie (catégorie : total sur N dépenses) :',
                'recurring_in' => 'Dépenses RÉCURRENTES payées DANS cette période :',
                'recurrent_header' => 'Dépenses RÉCURRENTES (>= 3 périodes différentes) avec leurs PÉRIODES PRÉCISES :',
                'employees' => 'EMPLOYÉS (effectif réel de la plateforme) :',
                'closures' => 'CLÔTURES MENSUELLES OFFICIELLES (chiffres figés validés par le gérant) :',
                'incomes_summary' => "ENTRÉES D'ARGENT (détail exhaustif des entrées ACTIVES sur la couverture) :",
                'coverage' => "COUVERTURE DE TES CONNAISSANCES : tu connais UNIQUEMENT ce qui précède. Ce que tu ne vois PAS : notes privées des dépenses, données supprimées (corbeille), mots de passe, données d'autres entreprises, périodes plus anciennes que celles listées. ",
                'hidden_warn' => "IMPORTANT : ton rôle ne donne PAS accès à : %s. Si on te demande une de ces informations, réponds « Cette information nécessite une permission que ton compte n'a pas » — n'ESTIME jamais ces chiffres.",
                'out_of_scope' => " Pour toute question hors couverture : « cette précision n'est pas dans mes données ».",
                'empty_period' => 'Période %s (du %s au %s) : AUCUNE donnée enregistrée (ni dépense ni entrée). Si on te demande cette période, réponds qu\'elle est vide.',
                'none_on_period' => 'AUCUNE donnée enregistrée (0).',
                'module_contracts' => 'Contrats : non inclus dans le contexte.',
                'module_commitments' => 'Engagements mensuels : non inclus.',
                'module_investments' => 'Investissements : non inclus.',
            ],
        };
    }

    /** Rend le label i18n avec substitution des placeholders. */
    public function get(string $key, ...$args): string
    {
        $template = $this->labels[$key] ?? $key;
        return $args ? sprintf($template, ...$args) : $template;
    }
}
