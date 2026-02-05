<?php

namespace App\Http\Controllers;

use App\Models\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class FileShareController extends Controller
{
    public function index()
    {
        $files = PublicFile::latest()->get();
        return view('file-share.index', compact('files'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            // $extension = $file->getClientOriginalExtension();
            $name = pathinfo($originalName, PATHINFO_FILENAME);
            
            $slug = Str::random(10);
            while (PublicFile::where('slug', $slug)->exists()) {
                $slug = Str::random(10);
            }

            $path = $file->store('public_files');

            $publicFile = PublicFile::create([
                'name' => $name,
                'original_name' => $originalName,
                'path' => $path,
                'mime_type' => $mimeType,
                'size' => $size,
                'slug' => $slug,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $publicFile,
                'public_url' => $publicFile->public_url
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    public function show($slug)
    {
        $file = PublicFile::where('slug', $slug)->firstOrFail();
        $path = storage_path('app/' . $file->path);

        if (!Storage::exists($file->path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"'
        ]);
    }

    public function download($slug)
    {
        $file = PublicFile::where('slug', $slug)->firstOrFail();
        $path = storage_path('app/' . $file->path);

        if (!Storage::exists($file->path)) {
            abort(404);
        }

        return response()->download($path, $file->original_name);
    }

    public function destroy($id)
    {
        $file = PublicFile::findOrFail($id);
        Storage::delete($file->path);
        $file->delete();

        return response()->json(['success' => true]);
    }
}
