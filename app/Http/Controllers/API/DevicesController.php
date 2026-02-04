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
        $part   = $request->get('part');
        $data   = $request->get('data');
        $finish = $request->get('finish');

        $cacheKey = "screen_parts_{$mac}_{$androidid}";

        Log::info("MAC: " . $mac);
        Log::info("ANDROID: " . $androidid);
        Log::info("PART: " . $part);
        Log::info("FINISH: " . $finish);

        // ambil data lama dari cache
        $parts = Cache::get($cacheKey, []);

        // simpan part ke cache
        if ($part !== null && $data) {
            $parts[$part] = urldecode($data); // penting
            Cache::put($cacheKey, $parts, now()->addMinutes(5));

            return response()->json([
                'status' => 'part_saved',
                'part'   => $part
            ]);
        }

        // jika finish = 1 → gabung semua part
        if ($finish == 1) {

            if (empty($parts)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no parts found'
                ], 400);
            }

            ksort($parts); // urutkan part 0,1,2,...

            $base64 = implode('', $parts);

            $image = base64_decode($base64);

            $filename = "screen_{$mac}_{$androidid}_" . time() . ".png";

            Storage::disk('public')->put($filename, $image);

            // hapus cache setelah selesai
            Cache::forget($cacheKey);

            return response()->json([
                'status' => 'done',
                'file'   => $filename
            ]);
        }

        return response()->json(['status' => 'waiting']);
    }


}
