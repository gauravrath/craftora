<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\MediaFile;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov,webm|max:102400',
        ]);

        $file = $request->file('media');

        // Upload file to S3
        $path = Storage::disk('s3')->putFile(
            'uploads/media',
            $file
        );

        // Save file information in database
        $media = MediaFile::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'File uploaded successfully',
            'data' => $media,
        ]);
    }

    public function index()
    {
        $mediaFiles = MediaFile::latest()->get();

        foreach ($mediaFiles as $media) {
            $media->url = Storage::disk('s3')->temporaryUrl(
                $media->file_path,
                now()->addMinutes(30)
            );
        }

        return view('media.index', compact('mediaFiles'));
    }

    public function delete($id)
    {
        $media = MediaFile::findOrFail($id);

        Storage::disk('s3')->delete($media->file_path);

        $media->delete();

        return back()->with('success', 'File deleted successfully');
    }
}