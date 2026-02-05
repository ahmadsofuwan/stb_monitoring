<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Devices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            // Ubah ke Base64 agar aman dikirim
            $script = base64_encode(str_replace("\r", '', $device->script));
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

       
       

       return response($script, 200)->header('Content-Type', 'text/plain');
    }

    public function storeScreenshot(Request $request)
    {
        $android_id = $request->android_id;
        $mac_address = $request->mac;

        $device = Devices::where('android_id', $android_id)->orWhere('mac_address', $mac_address)->first();
        if(!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        if($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $android_id . '.png';
            $file->move(public_path('screenshots'), $filename);

            \App\Models\Screenshot::create([
                'device_id' => $device->id,
                'filename' => $filename,
            ]);

            return response()->json(['message' => 'Screenshot saved'], 200);
        }

        // Jika kirim raw body (wget --post-file sering kirim raw body jika tidak pakai multipart)
        $rawData = $request->getContent();
        if($rawData) {
            $filename = time() . '_' . $android_id . '.png';
            file_put_contents(public_path('screenshots/' . $filename), $rawData);

            \App\Models\Screenshot::create([
                'device_id' => $device->id,
                'filename' => $filename,
            ]);

            return response()->json(['message' => 'Screenshot saved (raw)'], 200);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function realtimescreen(Request $request, $mac, $androidid)
    {
        Log::info('mac: '. $mac .' androidid: '. $androidid );
        $device = Devices::where('android_id', $androidid)->orWhere('mac_address', $mac)->first();
        
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'device not found'], 404);
        }

        // Mendukung berbagai metode upload dari curl
        if ($request->hasFile('file')) {
            // Multipart upload: curl -F "file=@image.png"
            $image = file_get_contents($request->file('file')->getRealPath());
        } elseif ($request->has('data') && !empty($request->get('data'))) {
            // Base64 field: curl -d "data=BASE64DATA..."
            $image = base64_decode($request->get('data'));
        } else {
            // Raw body: curl --data-binary "@image.png"
            $image = $request->getContent();
        }

        if ($image && strlen($image) > 0) {
            $filename = "screen_" . $device->id . ".png";
            Storage::disk('public')->put($filename, $image);

            return response()->json([
                'status' => 'done',
                'file'   => $filename,
                'url'    => asset('storage/' . $filename) . '?v=' . time()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'no image data found'
        ], 400);
    }

    public function statusrealtime($mac, $androidid)
    {
        $status = Cache::get("realtime_{$mac}_{$androidid}");
        if (!$status) {
            return response()->json(['status' => 'stop']);
        }
        return response()->json(['status' => 'running']);
    }


}
