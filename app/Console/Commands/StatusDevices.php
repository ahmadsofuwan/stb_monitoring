<?php

namespace App\Console\Commands;

use App\Models\Devices;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
        $chace  = Cache::get('realtime_a4:40:27:08:e7:be_1759335266.00912');
        if ($chace) {
            dd($chace);
        }else{
            $this->info('nothing');
        }
    }
    
}
