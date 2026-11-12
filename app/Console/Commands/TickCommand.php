<?php

namespace App\Console\Commands;

use App\Support\CloudSync;
use App\Support\License;
use Illuminate\Console\Command;

/** شغل الخلفية: نبض الترخيص لو مستحق + رفع المزامنة السحابية (بيتشغل من EnsureLicense::terminate في عملية منفصلة) */
class TickCommand extends Command
{
    protected $signature = 'ns:tick {--sync}';
    protected $description = 'License heartbeat and cloud sync in the background';

    public function handle(): int
    {
        License::heartbeatDue() && License::heartbeat();
        $this->option('sync') && CloudSync::enabled() && CloudSync::push();
        return self::SUCCESS;
    }
}