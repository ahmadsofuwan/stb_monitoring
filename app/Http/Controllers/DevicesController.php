<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use App\Models\Script;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class DevicesController extends Controller
{
    public function index(Request $request)
    {
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
                    $btn .= '<a class="dropdown-item delete text-danger" href="javascript:void(0)" data-id="' . encrypt($row->id) . '">Delete</a>';
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('devices.index');
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
}
