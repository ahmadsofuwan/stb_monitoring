<?php

namespace App\Console\Commands;

use App\Models\Devices;
use Illuminate\Console\Command;

class StatusDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:status-devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Devices::where('last_active_at', '<', now()->subMinutes(5))->update([
            'status' => 'offline',
        ]);
    }
    
}
