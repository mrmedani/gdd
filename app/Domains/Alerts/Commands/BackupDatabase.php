<?php

namespace App\Domains\Alerts\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Automated database backup';

    public function handle(): int
    {
        $connection = config('database.default');
        $config = config('database.connections.' . $connection);

        $backupsDir = storage_path('app/backups');
        if (!is_dir($backupsDir)) {
            mkdir($backupsDir, 0755, true);
        }

        $filename = 'backup-' . now()->format('Y-m-d-H-i-s');

        if ($connection === 'sqlite') {
            $dbPath = $config['database'];

            if (!file_exists($dbPath)) {
                $this->error("Database file not found: {$dbPath}");
                return self::FAILURE;
            }

            $backupPath = "{$backupsDir}/{$filename}.sqlite";
            copy($dbPath, $backupPath);
            $this->info("Backup created: {$filename}.sqlite");
        } else {
            $backupPath = "{$backupsDir}/{$filename}.sql";

            try {
                $this->exportMySql($backupPath);
                $this->info("Backup created: {$filename}.sql");
            } catch (\Throwable $e) {
                $this->error('Backup failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        }

        $this->pruneOldBackups($backupsDir);

        return self::SUCCESS;
    }

    protected function exportMySql(string $backupPath): void
    {
        $pdo = DB::connection()->getPdo();
        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        $handle = fopen($backupPath, 'w');
        fwrite($handle, "-- Database backup generated on " . now()->format('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Engine: MySQL\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
        fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

        foreach ($tables as $table) {
            $tableSafe = str_replace('`', '``', $table);
            $this->line("  Dumping: {$table}");

            $createStmt = $pdo->query("SHOW CREATE TABLE `{$tableSafe}`")->fetch(\PDO::FETCH_ASSOC);
            $createSql = $createStmt['Create Table'];

            fwrite($handle, "\n-- Table: {$table}\n\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableSafe}`;\n");
            fwrite($handle, $createSql . ";\n\n");

            $rows = $pdo->query("SELECT * FROM `{$tableSafe}`")->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                continue;
            }

            $columns = array_keys($rows[0]);
            $colNames = implode('`, `', $columns);
            $chunkSize = 100;
            $chunks = array_chunk($rows, $chunkSize);

            foreach ($chunks as $chunk) {
                $values = [];
                foreach ($chunk as $row) {
                    $escaped = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $escaped[] = 'NULL';
                        } else {
                            $escaped[] = $pdo->quote((string) $value);
                        }
                    }
                    $values[] = '(' . implode(', ', $escaped) . ')';
                }
                fwrite($handle, "INSERT INTO `{$tableSafe}` (`{$colNames}`) VALUES\n" . implode(",\n", $values) . ";\n\n");
            }

            unset($rows, $chunks);
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);
    }

    protected function pruneOldBackups(string $backupsDir): void
    {
        $backups = array_merge(
            glob("{$backupsDir}/*.sql") ?: [],
            glob("{$backupsDir}/*.sqlite") ?: []
        );

        usort($backups, fn($a, $b) => filemtime($b) - filemtime($a));

        $maxBackups = 30;
        while (count($backups) > $maxBackups) {
            $oldest = array_pop($backups);
            unlink($oldest);
            $this->info("Removed old backup: " . basename($oldest));
        }
    }
}
