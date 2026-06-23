<?php

namespace App\Http\Controllers;

use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function store(Request $request, SupabaseStorageService $storage)
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'type' => 'required|in:product,logo',
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension() ?: $file->extension();
        $path = 'user_' . $request->user()->id . '/' . $validated['type'] . '_' . Str::uuid() . '.' . $ext;

        try {
            $url = $storage->upload($path, $file->get(), $file->getMimeType());
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah gambar. Coba lagi.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'url' => $url,
            'type' => $validated['type'],
        ]);
    }
}
