<?php

namespace App\Http\Controllers;

use App\Models\Devices;
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
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm" data-id="' . encrypt($row->id) . '">Script</a>';
                    $btn .= ' <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" data-id="' . encrypt($row->id) . '">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('devices.index');
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
