<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Devices;
use Illuminate\Http\Request;

class DevicesController extends Controller
{
    public function index(Request $request)
    {
       $android_id = $request->android_id;
       $mac_address = $request->mac;
       $script = "";

       $device = Devices::where('android_id', $android_id)->orWhere('mac_address', $mac_address)->first();
       if($device){
        if($device->script != null){
            $script = $device->script;
        }
        $device->update([
            'last_active_at' => now(),
            'script' => null,
            'status' => 'online',
        ]);
       }else{
        $device = Devices::create([
            'android_id' => $android_id,
            'mac_address' => $mac_address,
            'last_active_at' => now(),
            'status' => 'online',
        ]);
       }
       

       return response($script, 200)
       ->header('Content-Type', 'text/plain')
       ->header('Content-Type', 'text/plain; charset=utf-8')
       ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
