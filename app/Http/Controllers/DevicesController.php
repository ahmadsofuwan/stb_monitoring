<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use App\Models\Script;
use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class DevicesController extends Controller
{
    public function index(Request $request)
    {
        // Mark inactive devices as offline
        Devices::where('status', 'online')
            ->where('last_active_at', '<', now()->subMinutes(5))
            ->update(['status' => 'offline']);

        if ($request->ajax()) {
            $data = Devices::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="form-check-input device-checkbox" value="' . encrypt($row->id) . '">';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">Action <i class="bx bx-chevron-down"></i></button>';
                    $btn .= '<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">';
                    $btn .= '<a class="dropdown-item edit" href="javascript:void(0)" data-id="' . encrypt($row->id) . '">Edit/Push Custom</a>';
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<h6 class="dropdown-header">Push Saved Script</h6>';
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<a class="dropdown-item" href="' . route('devices.remote', encrypt($row->id)) . '">Remote Control</a>';
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<a class="dropdown-item delete text-danger" href="javascript:void(0)" data-id="' . encrypt($row->id) . '">Delete</a>';
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->rawColumns(['action', 'checkbox'])
                ->make(true);
        }
        $scripts = Script::all();
        return view('devices.index', compact('scripts'));
    }

    public function pushScript(Request $request)
    {
        $device = Devices::find(decrypt($request->device_id));
        $script = Script::find(decrypt($request->script_id));
        
        $device->update([
            'script' => $script->content,
        ]);

        return response()->json([
            'message' => 'Script pushed to device successfully',
        ]);
    }

    public function update(Request $request, string $id)
    {
        $device = Devices::find(decrypt($id));
        $device->update([
            'script' => $request->script,
        ]);
        return redirect()->route('devices.index');
    }

    public function store(Request $request)
    {
        $deviceIds = $request->input('device_ids');

        if ($deviceIds) {
            $ids = array_map(function($id) {
                return decrypt($id);
            }, explode(',', $deviceIds));

            Devices::whereIn('id', $ids)->update([
                'script' => $request->script,
            ]);
            Alert::success('Success', 'Selected devices updated successfully');
        } else {
            Devices::whereNotNull('id')->update([
                'script' => $request->script,
            ]);
            Alert::success('Success', 'All devices updated successfully');
        }
        
        return redirect()->route('devices.index');
    }

    public function destroy($id)
    {
        $device = Devices::find(decrypt($id));
        $device->delete();
        return response()->json([
            'message' => 'Device deleted successfully',
        ]);
    }

    public function show(string $id)
    {
        $device = Devices::find(decrypt($id));
        return response()->json($device);
    }

    public function takeScreenshot(string $id)
    {
        $device = Devices::find(decrypt($id));
        
        $serverUrl = config('app.url') . '/api/screenshot?android_id=' . $device->android_id . '&mac=' . $device->mac_address;
        
        // Android shell script using wget
        $script = "#!/system/bin/sh\n";
        $script .= "screencap -p /sdcard/screenshot.png\n";
        $script .= "wget --post-file=/sdcard/screenshot.png \"$serverUrl\"\n";
        $script .= "rm /sdcard/screenshot.png\n";

        $device->update([
            'script' => $script,
        ]);

        return response()->json([
            'message' => 'Screenshot request sent to device',
        ]);
    }

    public function showScreenshots(string $id)
    {
        $device = Devices::find(decrypt($id));
        $screenshots = Screenshot::where('device_id', $device->id)
            ->latest()
            ->get()
            ->map(function($item) {
                return [
                    'filename' => $item->filename,
                    'created_at' => $item->created_at->diffForHumans(),
                ];
            });
            
        return response()->json($screenshots);
    }

    public function remote(string $id)
    {

        $device = Devices::find(decrypt($id));
        $latestScreenshot = Screenshot::where('device_id', $device->id)->latest()->first();
        $scripts = Script::all();
        $cacheName = "realtime_{$device->mac_address}_{$device->android_id}";

        Cache::forget("realtime_{$device->mac_address}_{$device->android_id}_stop");
        Cache::put($cacheName, "", 3600);

        return view('devices.remote', compact('device', 'latestScreenshot', 'scripts'));
    }

    public function sendRemoteCommand(Request $request)
    {
        $device = Devices::find(decrypt($request->device_id));
        $command = $request->command;
        $type = $request->input('type', 'key'); // 'key' or 'text'

        if ($type == 'text') {
            $script = "input text \"" . str_replace('"', '\"', $command) . "\"";
        } else {
            $script = "input keyevent $command";
        }

        Cache::put("realtime_{$device->mac_address}_{$device->android_id}", $script, 3600);

        // Update history
        $historyKey = "history_{$device->mac_address}_{$device->android_id}";
        $history = Cache::get($historyKey, []);
        array_unshift($history, [
            'command' => $script,
            'time' => now()->format('H:i:s')
        ]);
        $history = array_slice($history, 0, 10); // Keep last 10
        Cache::put($historyKey, $history, 3600);

        return response()->json([
            'message' => 'Command sent successfully',
            'script' => $script
        ]);
    }

    public function getRemoteStatus(Request $request, $id)
    {
        $device = Devices::find(decrypt($id));
        $script = Cache::get("realtime_{$device->mac_address}_{$device->android_id}", "");
        $history = Cache::get("history_{$device->mac_address}_{$device->android_id}", []);

        return response()->json([
            'current_script' => $script,
            'history' => $history
        ]);
    }

    public function stopRemote(Request $request)
    {
        $device = Devices::find(decrypt($request->device_id));
        $cacheName = "realtime_{$device->mac_address}_{$device->android_id}";
        
        Cache::put("{$cacheName}_stop", true, 3600);
        Cache::forget($cacheName);

        return response()->json([
            'message' => 'Remote stopped successfully'
        ]);
    }
}
