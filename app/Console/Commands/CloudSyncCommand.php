<?php

namespace App\Console\Commands;

use App\Support\CloudSync;
use App\Support\License;
use Illuminate\Console\Command;

class CloudSyncCommand extends Command
{
    protected $signature = 'cloud:sync {--heartbeat : حدّث حالة الاشتراك أولاً}';
    protected $description = 'مزامنة Cloud Pro وتحديث الاشتراك';

    public function handle(): int
    {
        if ($this->option('heartbeat')) {
            License::heartbeat();
        }
        [$ok, $msg] = CloudSync::push();
        $this->line($msg);
        return $ok ? 0 : 1;
    }
}
