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

    public function render()
    {
        return view('livewire.database-backup')
            ->layout('layouts.app')
            ->title(__('settings.backup_title'));
    }
}
