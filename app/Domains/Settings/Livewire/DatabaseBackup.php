<?php

namespace App\Domains\Settings\Livewire;

use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class DatabaseBackup extends Component
{
    use WithToast;
    public array $backups = [];
    public string $statusMessage = '';
    public bool $isCreating = false;

    public function mount(): void
    {
        Gate::authorize('manage-backups');
        $this->loadBackups();
    }

    public function loadBackups(): void
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            $this->backups = [];
            return;
        }

        $files = array_merge(
            glob($dir . '/*.sql') ?: [],
            glob($dir . '/*.sqlite') ?: [],
            glob($dir . '/*.gz') ?: [],
        );

        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        $this->backups = array_map(fn($path) => [
            'path' => $path,
            'name' => basename($path),
            'size' => $this->formatSize(filesize($path)),
            'date' => date('Y-m-d H:i:s', filemtime($path)),
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
        ], $files);
    }

    protected function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function createBackup(): void
    {
        $this->isCreating = true;
        $this->statusMessage = '';

        try {
            Artisan::call('backup:database');
            $this->statusMessage = __('settings.backup_created');
            $this->notify(__('settings.backup_created'));
        } catch (\Throwable $e) {
            $this->statusMessage = __('settings.backup_failed') . ': ' . $e->getMessage();
        }

        $this->isCreating = false;
        $this->loadBackups();
    }

    public function deleteBackup(string $filename): void
    {
        $path = storage_path('app/backups/' . basename($filename));
        if (!file_exists($path)) {
            $this->loadBackups();
            return;
        }

        unlink($path);
        $this->loadBackups();
        $this->notify(__('common.deleted'));
    }

    public function restoreBackup(string $filename): void
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            $this->statusMessage = __('settings.backup_not_found');
            $this->loadBackups();
            return;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== 'sql') {
            $this->statusMessage = __('settings.backup_restore_sql_only');
            return;
        }

        // Backup automatique de securite avant restauration
        try {
            Artisan::call('backup:database');
        } catch (\Throwable $e) {
            // non bloquant
        }

        $config = config('database.connections.' . config('database.default'));
        $host = $config['host'] ?? '127.0.0.1';
        $port = (string) ($config['port'] ?? 3306);
        $db = $config['database'];
        $user = $config['username'];
        $pass = $config['password'];

        $ok = false;
        $error = '';

        // 1) Tentative via mysql CLI
        $cmd = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($db)
        );

        $descriptors = [
            0 => ['file', $path, 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open($cmd, $descriptors, $pipes);
        if (is_resource($process)) {
            $error = (string) stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);
            $ok = ($exitCode === 0);
        } else {
            $error = 'mysql CLI indisponible';
        }

        // 2) Fallback PDO si mysql CLI a echoue
        if (!$ok) {
            try {
                $this->restoreViaPdo($path);
                $ok = true;
                $error = '';
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        if ($ok) {
            $this->statusMessage = __('settings.backup_restored');
            $this->notify(__('settings.backup_restored'));
        } else {
            $this->statusMessage = __('settings.backup_restore_failed') . ': ' . trim($error);
        }

        $this->loadBackups();
    }

    protected function restoreViaPdo(string $path): void
    {
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $sql = (string) file_get_contents($path);
        foreach ($this->splitSql($sql) as $stmt) {
            if (trim($stmt) !== '') {
                $pdo->exec($stmt);
            }
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function splitSql(string $sql): array
    {
        $sql = preg_replace('/^--.*$/m', '', $sql); // retire commentaires --
        $lines = explode("\n", $sql);
        $statements = [];
        $current = '';
        foreach ($lines as $line) {
            $current .= $line . "\n";
            if (str_ends_with(rtrim($line), ';')) {
                $statements[] = $current;
                $current = '';
            }
        }
        if (trim($current) !== '') {
            $statements[] = $current;
        }
        return $statements;
    }

    public function render()
    {
        return view('livewire.database-backup')
            ->layout('layouts.app')
            ->title(__('settings.backup_title'));
    }
}
