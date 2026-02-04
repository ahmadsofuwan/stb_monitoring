<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use App\Models\Script;
use App\Models\Screenshot;
use Illuminate\Http\Request;
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
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">Action <i class="bx bx-chevron-down"></i></button>';
                    $btn .= '<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">';
                    $btn .= '<a class="dropdown-item edit" href="javascript:void(0)" data-id="' . encrypt($row->id) . '">Edit/Push Custom</a>';
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<h6 class="dropdown-header">Push Saved Script</h6>';
                    
                    $scripts = Script::all();
                    foreach ($scripts as $script) {
                        $btn .= '<a class="dropdown-item push-saved-script" href="javascript:void(0)" data-device-id="' . encrypt($row->id) . '" data-script-id="' . encrypt($script->id) . '">' . $script->name . '</a>';
                    }
                    
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<a class="dropdown-item take-screenshot" href="javascript:void(0)" data-id="' . encrypt($row->id) . '">Take Screenshot</a>';
                    $btn .= '<a class="dropdown-item view-screenshots" href="javascript:void(0)" data-id="' . encrypt($row->id) . '">View Screenshots</a>';
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<a class="dropdown-item delete text-danger" href="javascript:void(0)" data-id="' . encrypt($row->id) . '">Delete</a>';
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
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
        Devices::whereNotNull('id')->update([
            'script' => $request->script,
        ]);
        Alert::success('Success', 'Device updated successfully');
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
}
