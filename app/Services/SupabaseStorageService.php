<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseStorageService
{
    private string $url;
    private string $key;
    private string $bucket;

    public function __construct()
    {
        $this->url = rtrim((string) config('services.supabase.url'), '/');
        $this->key = (string) config('services.supabase.key');
        $this->bucket = (string) config('services.supabase.bucket');
    }

    /**
     * Upload raw bytes ke Supabase Storage. Mengembalikan public URL.
     *
     * @throws RuntimeException jika upload gagal.
     */
    public function upload(string $path, string $contents, string $contentType): string
    {
        $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$path}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
            'Content-Type' => $contentType,
            'x-upsert' => 'true',
        ])->withBody($contents, $contentType)->post($endpoint);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Gagal upload ke Supabase Storage: ' . $response->status() . ' ' . $response->body()
            );
        }

        return $this->publicUrl($path);
    }

    public function publicUrl(string $path): string
    {
        return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}";
    }
}
