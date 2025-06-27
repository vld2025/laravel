<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class TestBackup extends Command
{
    protected $signature = 'backup:test';
    
    protected $description = 'Testa il sistema di backup su NAS';
    
    public function handle()
    {
        $this->info('🔧 Test connessione NAS...');
        
        $nasPath = '/mnt/synology-backup/VLD-Backup';
        
        if (!is_dir($nasPath)) {
            $this->error("❌ Directory NAS non trovata: {$nasPath}");
            return Command::FAILURE;
        }
        
        if (!is_writable($nasPath)) {
            $this->error("❌ Directory NAS non scrivibile: {$nasPath}");
            return Command::FAILURE;
        }
        
        $this->info("✅ Connessione NAS OK!");
        
        // Test scrittura file
        $testFile = $nasPath . '/test_' . time() . '.txt';
        file_put_contents($testFile, 'Test backup VLD Service');
        
        if (file_exists($testFile)) {
            $this->info("✅ Scrittura su NAS OK!");
            unlink($testFile);
        } else {
            $this->error("❌ Impossibile scrivere su NAS");
            return Command::FAILURE;
        }
        
        // Test BackupService
        try {
            $this->info('🔧 Test BackupService...');
            $backupService = new BackupService();
            
            // Test backup database
            $this->info('📁 Test backup database...');
            $backupService->backupDatabase();
            $this->info('✅ Backup database OK!');
            
            $this->info('');
            $this->info('🎉 Tutti i test completati con successo!');
            
        } catch (\Exception $e) {
            $this->error("❌ Errore: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
