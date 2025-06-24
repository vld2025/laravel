
    protected function schedule(Schedule $schedule)
    {
        // Controlla automazione PDF ogni ora
        $schedule->command('spese:invia-mensili')
                 ->hourly()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/automazione-pdf.log'));
    }
