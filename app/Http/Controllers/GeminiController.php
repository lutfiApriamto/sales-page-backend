<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    public function askGemini(Request $request)
    {
        // 1. Tangkap pertanyaan dari user (atau pakai teks default)
        $prompt = $request->input('prompt', 'Buatkan 3 ide nama unik untuk produk sepatu olahraga.');

        // 2. Tembak API Gemini (Kita pakai model gemini-1.5-flash karena cepat dan murah/gratis)
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        // 3. Jika API berhasil merespons
        if ($response->successful()) {
            $data = $response->json();
            
            // Mengambil spesifik teks balasannya saja dari struktur JSON Gemini
            $textResult = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada jawaban dari AI';

            return response()->json([
                'status' => 'success',
                'jawaban_gemini' => $textResult
            ]);
        }

        // 4. Jika terjadi error (misal API key salah)
        return response()->json([
            'status' => 'error',
            'pesan' => 'Gagal menghubungi Gemini',
            'detail' => $response->json()
        ], 500);
    }
}