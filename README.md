# 📊 Gestion de Dépenses & Trésorerie — Chronorex Express

> **Application web complète** de gestion financière pour l'entreprise Chronorex Express : dépenses, trésorerie (caisse), employés, salaires, rapports PDF/Excel, et notifications multicanal (WhatsApp / Base de données).

![Laravel](https://img.shields.io/badge/Laravel-11-F9322C?logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-4-FB70A9?logo=livewire)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql)
![ECharts](https://img.shields.io/badge/ECharts-5-AA344D?logo=apacheecharts)

---

## 🧭 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Tech Stack](#-tech-stack)
- [Architecture](#-architecture)
- [Notifications](#-notifications)
- [Règles Métier](#-règles-métier)
- [Installation](#-installation)
- [Déploiement](#-déploiement)
- [Routes principales](#-routes-principales)
- [Commandes Artisan](#-commandes-artisan)
- [Paramètres système](#-paramètres-système)
- [Modèles de données](#-modèles-de-données)

---

## ✨ Fonctionnalités

### 💰 Gestion des Dépenses
- **CRUD complet** : ajout, modification, suppression (individuelle et groupée)
- **Catégories hiérarchiques** : organisation arborescente avec support multilingue (ar/fr/en)
- **Types de paiement** : espèces, chèque, virement, carte bancaire, etc.
- **Pièces jointes** : upload de reçus et justificatifs
- **Filtres avancés** : par date, catégorie, montant, employé, type de paiement
- **Sélection multiple** : suppression groupée avec confirmation

### 💵 Entrées d'argent (Incomes)
- **CRUD complet** : enregistrement des flux d'argent entrants (investissements, droits de franchise, autres)
- **Types de source** : `investment` (Investissement), `franchise_fee` (Droits de franchise), `other` (Autre)
- **Sous-type conditionnel** : champ `sub_type` affiné selon le type de source
- **Nom de la source** : investisseur / franchisé / bénéficiaire
- **Cartes KPI** : total de la période, total par type de source
- **Visibilité par rôle** : page `/incomes` masquée aux rôles sans la permission `incomes` (toggle dans `/settings/roles`)
- **Intégration trésorerie** : le total des entrées est automatiquement inclus dans la clôture mensuelle (`calculatedIncomes`)

### 🏦 Trésorerie (Caisse)
- **Suivi du solde** : dépenses quotidiennes, gains, balance mensuelle
- **Clôture mensuelle** : génération automatique du bilan mensuel
- **Période comptable personnalisée** : configurable (ex: du 21 au 20)
- **Déficit / Surplus** : détection automatique et alertes
- **Historique complet** : suivi de toutes les clôtures mensuelles
- **Calcul automatique du solde** : `Solde = Gains du mois (saisi) + Total entrées (auto, depuis /incomes) − Total des charges (auto)`. Le champ "Gains du mois (Revenus)" est saisi manuellement ; les entrées et charges sont calculées automatiquement depuis leurs tables respectives.

### 👥 Employés & Salaires
- **Gestion des employés** : fiche complète (coordonnées, salaire de base, date d'embauche)
- **Avances sur salaire** : enregistrement, déduction automatique, suivi
- **Paiements de salaire** : génération avec calcul automatique des déductions
- **Rappels de paie automatiques** : notifications programmées

### 📈 Rapports & Statistiques
- **Rapports PDF** : générés avec DomPDF, design professionnel (barre rose)
- **Rapports Excel** : export via Laravel Excel (barre verte)
- **Période au choix** : sélecteur des 12 dernières périodes avec labels formatés
- **Statistiques détaillées** : graphiques, tendances, répartition par catégorie
- **Section « Entrées d'argent » combinée** : sur `/statistics`, une seule section regroupe les cartes KPI + le camembert de répartition par type de source + la courbe de tendance (12 mois) des entrées `/incomes`
- **Aperçu instantané** : gains, dépenses, solde, répartition avant téléchargement

### 📊 Tableau de Bord
- **Indicateurs KPI** : 4 cartes (total mensuel, moyenne quotidienne, dépenses du jour, utilisateurs)
- **Graphiques** : répartition par catégorie (camembert), tendance mensuelle (linéaire)
- **Dernières dépenses** : liste des 5 dernières avec lien "Voir tout"
- **Alertes récentes** : affichage des 3 dernières notifications
- **Navigation contextuelle** : clic sur les cartes → filtre automatique dans la liste

### 🎨 Interface & Expérience Utilisateur
- **Dark Mode** : basculement avec persistance (localStorage + préférence système)
- **PWA (Progressive Web App)** : installable sur mobile/desktop, manifest.json dynamique
- **Responsive Design** : sidebar adaptative, bottom sheets sur mobile, safe-areas iOS
- **Multilingue** : français, anglais, arabe — commutation en direct
- **Notifications toast** : feedback utilisateur après chaque action
- **Thème personnalisable** : logo, favicon, couleurs PWA, langue par défaut

### 🔐 Sécurité & Administration
- **Authentification** : email/mot de passe avec rate limiting (3 tentatives/min)
- **Rôles & Permissions** : Admin, Comptable, et rôles personnalisés — permissions granulaires par module
- **Permissions d'accès aux pages** : la page `/incomes` (et toutes les pages) est masquée aux rôles sans la permission correspondante (toggle dans `/settings/roles`, section « Permissions d'accès aux pages »)
- **Permissions d'actions** : toggles séparés pour les actions sensibles (vue déficit, suppression de clôture, connexion en tant que…)
- **Journal d'audit** : traçabilité complète de toutes les actions (création, modification, suppression, connexion)
- **Gestion des utilisateurs** : CRUD avec attribution de rôles
- **Pop-up de connexion** : message personnalisable affiché après login
- **Sauvegarde de base de données** : via commande Artisan dédiée

### 💾 Sauvegarde & Restauration
- **Sauvegardes MySQL** : export complet de la base en `.sql` (avec `DROP TABLE` + `INSERT`), stocké dans `storage/app/backups/`
- **Création en 1 clic** : bouton « Créer la sauvegarde » sur `/settings/database-backup`
- **Restauration par sauvegarde** : chaque sauvegarde liste dispose d'un bouton **Restaurer** 🔵
  - **Confirmation** demandée avant écrasement des données
  - **Sauvegarde automatique de sécurité** générée avant chaque restauration (point de retour)
  - **Import** via `mysql` CLI, avec **fallback PDO** si le binaire `mysql` n'est pas disponible (ex : hébergement mutualisé cPanel)
  - Format `.sql` (MySQL) uniquement — les `.sqlite` sont refusés avec message clair
- **Téléchargement & Suppression** : chaque sauvegarde peut être téléchargée ou supprimée
- **Rétention** : les 30 sauvegardes les plus récentes sont conservées (les plus anciennes sont purgées automatiquement)

---

## 🚀 Tech Stack

| Technologie | Utilisation |
|---|---|
| **Laravel 11** | Framework PHP backend |
| **Livewire 3** | Composants full-stack dynamiques |
| **Alpine.js** | Interactivité frontend légère |
| **Tailwind CSS 4** | Design system utility-first |
| **MySQL** | Base de données relationnelle |
| **DomPDF** | Génération de rapports PDF |
| **Laravel Excel** | Export de rapports Excel |
| **ECharts 5** | Graphiques interactifs (dashboard, statistiques) |
| **Puppeteer + whatsapp-web.js** | Worker Node.js pour notifications WhatsApp |
| **PWA** | Service Worker + manifest.json |

---

## 🎨 Design System

### Product Register (Impeccable Design)
La charte UI suit un registre produit strict pour éliminer les patterns "AI-slop" :

| Règle | Description |
|---|---|
| **Couleur retenue** | Palette restreinte, pas de dégradés comme fond par défaut |
| **Pas de glassmorphism** | `backdrop-blur` interdit sauf cas fonctionnel explicite |
| **Pas de texte en dégradé** | `text-transparent bg-clip-text bg-gradient-to-r` → `text-slate-800` |
| **Cartes** | `rounded-2xl` max (pas `rounded-3xl`), `shadow-sm` suffit |
| **Pas d'animations décoratives** | Pas de `animate-ping`, `hover:scale`, `animate-fade-in` au chargement |
| **Pas d'orbes décoratifs** | Pas de `blur-3xl` / cercles floutés en arrière-plan |
| **Pas de bordures "fantômes"** | `border-white/60` interdit sur fond clair |
| **Navigation unifiée** | Tous les items de la sidebar utilisent `rounded-xl` avec `bg-slate-100` (actif) ou `bg-blue-600` (icône active) |
| **Étiquettes** | `font-semibold` au lieu de `font-bold` et `uppercase tracking-wider` |
| **Pas d'indicateurs latéraux** | Barres de `w-1` avec `shadow-[0_0_10px_#...]` supprimées |

### Helpers de période
- `formatPeriodLabel($start, $end)` → `"Du 21 janvier 2025 au 20 février 2025"`
- `formatPeriodLabelShort($start, $end)` → `"21 janv. → 20 fév. 2025"` (compact, utilisé dans les tableaux)

---

## 🏗️ Architecture

```
app/
├── Domains/
│   ├── Alerts/          # Notifications & canaux
│   │   ├── Channels/    # WhatsAppChannel
│   │   ├── Models/      # Alert (Eloquent)
│   │   ├── Notifications/ # 10 classes de notification
│   ├── Auth/            # Login, Forgot/Reset password
│   ├── Dashboard/       # KPI cards, alertes
│   ├── Employees/       # Employés, salaires, avances
│   ├── Expenses/        # CRUD dépenses, catégories, observers
│   ├── Reports/         # Rapports PDF/Excel
│   ├── Settings/        # Paramètres, utilisateurs, rôles, templates WhatsApp
│   ├── Statistics/      # Graphiques & tendances (dépenses + entrées)
│   └── Treasury/        # Caisse, clôtures mensuelles, observers (IncomeObserver)
├── Services/
│   └── WhatsAppService.php   # Client worker Node.js
└── Shared/
    └── Helpers/helpers.php   # Fonctions période, devise
```

### 📂 Structure des routes
- `routes/web.php` — Routes principales + proxy WhatsApp
- `routes/domains/auth.php` — Authentification
- Autres routes : déclarées dans `AppServiceProvider::boot()`

---

## 🔔 Notifications

### 10 Types d'alertes
| Notification | Déclencheur |
|---|---|
| `📝 Dépense créée` | Nouvelle dépense enregistrée |
| `✏️ Dépense modifiée` | Modification d'une dépense |
| `🗑️ Dépense supprimée` | Suppression d'une dépense |
| `💰 Entrée créée` | Nouvelle entrée d'argent (`/incomes`) enregistrée |
| `✏️ Entrée modifiée` | Modification d'une entrée d'argent |
| `🗑️ Entrée supprimée` | Suppression d'une entrée d'argent |
| `⚠️ Dépense élevée` | Seuil de montant dépassé |
| `💼 Rappel de paie` | Échéance de salaire |
| `📊 Rapport journalier` | Résumé périodique (journalier/hebdo/mensuel) |
| `📦 Clôture mensuelle` | Bilan de fin de période |

> **Cible des notifications WhatsApp pour `/incomes`** : seuls les utilisateurs appartenant à un **rôle ayant la permission `incomes`** ET ayant activé `notify_whatsapp` (avec un n° de téléphone renseigné) reçoivent ces alertes. Les rôles sans accès à `/incomes` ne sont jamais notifiés.

### Canaux de notification
- **💾 Base de données** — Stocké dans la table `alerts`, interface dédiée
- **💬 WhatsApp** — Envoi via worker Node.js (whatsapp-web.js + Puppeteer) sur le téléphone personnel de chaque utilisateur

### Worker WhatsApp
- **Technologie** : Node.js + Express + whatsapp-web.js + Puppeteer
- **Port** : 9090 (configurable)
- **Endpoints** : `/status`, `/qr`, `/send`, `/start`, `/disconnect`
- **Gestion** : PM2 (fork, auto-restart au boot via systemd) ou processus Windows (WMI)
- **Support Windows** : nécessite `PUPPETEER_EXECUTABLE_PATH` pointant vers Chrome (ex: `C:\Program Files\Google\Chrome\Application\chrome.exe`)
- **Heartbeat** : vérification d'état toutes les 30 secondes avec auto-recovery

---

## 📐 Règles Métier

### 📆 Période Comptable
- Configurable (défaut : **21 → 20** du mois suivant)
- Gérée via `getPeriodRange()`, `getPeriodFromDate()`, `formatPeriodLabel()`
- Protection contre le débordement des jours (format `Y-m-d` avec `-01`)

### 💰 Déficit / Surplus de Caisse
- À chaque clôture mensuelle :
  - **Solde négatif** → augmente le `cash_deficit`, notification WhatsApp
  - **Solde positif** → déduit du `cash_deficit`, notification WhatsApp
  - **Déficit entièrement couvert** → notification spéciale

### 🖼️ Toggles
- Utilisation de `wire:click="$toggle()"` + classes Blade conditionnelles
- Pas de `wire:model` + Alpine `$wire.entangle` (stabilité Livewire)

---

## 🔧 Installation

### Prérequis
- PHP 8.4+
- MySQL
- Composer
- Node.js (pour le worker WhatsApp)

```bash
git clone https://github.com/mrmedani/gdd.git gestion-chronorex
cd gestion-chronorex

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
php artisan storage:link  # Lien symbolique public/storage → storage/app/public
```

### Base de données
```bash
# Créer la base MySQL et configurer .env
php artisan migrate --seed
# ou importer le dump :
# mysql -u user -p db_name < chronorex.sql
```

### Lancer l'application
```bash
php artisan serve --port=2026
# Accès : http://localhost:2026
```

### Worker WhatsApp (optionnel)
```bash
cd whatsapp-worker
npm install
# Linux
node server.js
# Windows (définir le chemin Chrome d'abord)
set PUPPETEER_EXECUTABLE_PATH=C:\Program Files\Google\Chrome\Application\chrome.exe
node server.js
# Port : 9090
```

---

## 🚀 Déploiement (cPanel)

```bash
git pull origin main
php artisan optimize:clear
```

- Vérifier `allow_url_fopen = On` dans php.ini (pour les appels HTTP)
- Configurer CRON : `* * * * * cd /home/... && php artisan schedule:run >> /dev/null 2>&1`
- Worker WhatsApp : installer Node.js + PM2, lancer le worker comme processus persistant
- Vérifier le lien de stockage : `php artisan storage:link` (uploads logo, favicon, profiles)

---

## 🗺️ Routes principales

| Route | Action | Permission |
|---|---|---|
| `/` | Dashboard | auth |
| `/expenses` | Liste des dépenses | auth |
| `/expenses/create` | Nouvelle dépense | auth |
| `/incomes` | Entrées d'argent | incomes |
| `/reports` | Rapports PDF/Excel | reports |
| `/statistics` | Statistiques | statistics |
| `/treasury` | Trésorerie / Caisse | treasury |
| `/employees` | Employés | employees |
| `/settings` | Paramètres système | settings |
| `/settings/users` | Utilisateurs | users |
| `/settings/roles` | Rôles | roles |
| `/settings/categories` | Catégories | categories |
| `/settings/audit-logs` | Journal d'audit | audit-logs |
| `/settings/email-templates` | Templates email | email-templates |
| `/settings/database-backup` | Sauvegarde & Restauration DB | settings |
| `/profile` | Mon Profil (WhatsApp) | auth |
| `/manifest.json` | PWA manifest | public |

---

## ⚙️ Commandes Artisan

| Commande | Description |
|---|---|
| `alerts:high-expenses` | Vérifie les dépenses élevées |
| `alerts:salary-reminders` | Rappels de salaires |
| `alerts:missing-receipts` | Reçus manquants |
| `alerts:check-budgets` | Vérification budgets |
| `backup:database` | Sauvegarde de la base de données |
| `cache:clear` / `optimize:clear` | Nettoyage du cache |

---

## ⚙️ Paramètres système

| Clé | Description |
|---|---|
| `high_expense_threshold` | Seuil d'alerte dépenses élevées |
| `currency` | Devise (MAD) |
| `month_period_start_day` | Jour début de période (20) |
| `cash_deficit` | Manque en caisse |
| `app_name` | Nom de la plateforme |
| `app_logo` / `app_favicon` | Logo et favicon |
| `pwa_*` | Configuration PWA |
| `whatsapp_*` | Configuration WhatsApp |
| `default_locale` | Langue par défaut |
| `default_theme` | Thème par défaut |
| `login_popup_enabled` | Popup de connexion activé |
| `login_popup_content` | Contenu du popup |
| `whatsapp_message_delay` | Délai anti-spam WhatsApp |

---

## 🗄️ Modèles de données

- **User** — Utilisateurs (rôle, locale, téléphone WhatsApp, préférences)
- **Role** — Rôles + permissions personnalisées
- **Expense** — Dépenses (date, description, montant, catégorie, paiement, reçu)
- **Income** — Entrées d'argent (date, montant, type de source, nom de la source, sous-type)
- **ExpenseCategory** — Catégories (multilingue, hiérarchique)
- **Employee** — Employés (coordonnées, salaire, statut)
- **SalaryAdvance** — Avances sur salaire
- **SalaryPayment** — Paiements de salaire
- **MonthlyClosure** — Clôtures mensuelles
- **Alert** — Notifications (type, message, sévérité, lecture)
- **Setting** — Paramètres clé/valeur
- **AuditLog** — Journal d'activité

---

## 📝 Notes techniques

- **API externes** : utilisent `file_get_contents` + `stream_context_create` (pas Guzzle) pour compatibilité hébergement mutualisé
- **WhatsApp** : proxy PHP `/wa/*` pour éviter le mixed content (HTTPS→HTTP) côté navigateur ; backend communique directement avec le worker
- **Notifications WhatsApp** : chaque utilisateur reçoit les notifications sur son propre téléphone via `sendTo()`
- **Le canal WhatsApp** utilise les flags anti-detach Chrome, heartbeat 30s et auto-recovery pour la stabilité du worker
- **Graphiques ECharts** : remplacement de Chart.js par ECharts 5 sur le dashboard et la page statistiques (meilleure performance, rendu side-by-side)
- **Section statistiques combinée** : `/statistics` regroupe dans une seule section les KPI + camembert (répartition par type d'entrée) + courbe de tendance des entrées `/incomes`
- **Saisie de montants** : tous les champs monétaires acceptent les séparateurs de milliers (espace **et** virgule) — `87 104` ou `87,104` sont normalisés avant conversion (corrige un bug où `87 104` était lu comme `87`)
- **UI Design** : toutes les pages suivent le registre produit (`reference/product.md`) — pas de glassmorphism, dégradés, orbes, ou animations décoratives
- **Stockage des uploads** : le `storage:link` est requis pour servir le logo, favicon et photos de profil ; sans lui, les images retournent 404
- **Restauration de sauvegarde** : la commande `mysql` CLI est tentée en priorité ; si le binaire est absent (hébergement mutualisé), un fallback PDO exécute le `.sql` morceau par morceau (découpage sur `;` en ignorant les commentaires `--`). Une sauvegarde de sécurité est créée automatiquement avant chaque restauration.

---

## 📄 Licence

Projet privé — Chronorex Express © 2025–2026
