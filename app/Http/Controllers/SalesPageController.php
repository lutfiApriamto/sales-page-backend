<?php

namespace App\Http\Controllers;

use App\Models\SalesPage;
use App\Services\PromptBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesPageController extends Controller
{
    // 1. Mengambil semua riwayat Sales Page milik user yang sedang login (Read)
    public function index()
    {
        // Mengambil data urut dari yang paling baru
        $history = SalesPage::where('user_id', Auth::id())->latest()->get();
        return response()->json(['status' => 'success', 'data' => $history]);
    }

    // 2. Men-generate Sales Page via Gemini dan menyimpannya ke Database (Create)
        public function store(Request $request)
        {
            // 1. Validasi input dari form frontend
            $validated = $request->validate([
                'product_name' => 'required|string',
                'description' => 'required|string',
                'features' => 'nullable|array',
                'target_audience' => 'nullable|string',
                'price' => 'nullable|string',
                'unique_selling_points' => 'nullable|string',
            ]);

            // ==========================================
            // 2. CEK SISA CREDIT USER SEBELUM NEMBAK API
            // ==========================================
            $user = Auth::user();
            if ($user->credits <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Batas penggunaan API (35/35) telah habis untuk akun demo ini.'
                ], 403); 
            }

            // 3. Meracik Prompt Khusus untuk Gemini (System Prompt)
            $featuresText = isset($validated['features']) ? implode(", ", $validated['features']) : 'Tidak disebutkan';
            
            $prompt = "Anda adalah seorang World-Class Copywriter dan Conversion Rate Optimization (CRO) Expert dengan pengalaman 15 tahun yang djuga ahli dalam pembuatan website untuk membangun landing page yang menghasilkan jutaan dolar. Tugas Anda adalah menciptakan sales page yang SANGAT persuasif, emosional, dan mendorong konversi tinggi.

            DATA PRODUK:
            - Nama Produk: " . $validated['product_name'] . "
            - Deskripsi: " . $validated['description'] . "
            - Fitur Utama: " . $featuresText . "
            - Target Audiens: " . ($validated['target_audience'] ?? 'Umum') . "
            - Harga: " . ($validated['price'] ?? 'Hubungi untuk harga') . "
            - Unique Selling Points: " . ($validated['unique_selling_points'] ?? 'Tidak disebutkan') . "

            STRUKTUR WAJIB (ikuti urutan ini dengan ketat):

            1. HERO SECTION
            - Headline utama: Kalimat POWER yang menyentuh pain point terbesar audiens (maksimal 10 kata, gunakan angka jika relevan)
            - Sub-headline: Perjelas manfaat utama dan siapa yang akan terbantu (1-2 kalimat)

            2. PROBLEM SECTION
            - Gambarkan masalah yang dirasakan audiens dengan bahasa yang empatik dan relatable
            - Buat mereka merasa 'ini persis masalah saya'
            - Gunakan 3-4 bullet point pain point yang spesifik

            3. SOLUTION SECTION
            - Perkenalkan produk sebagai solusi terbaik
            - Jelaskan MENGAPA produk ini berbeda dari yang lain
            - Gunakan kalimat transisi yang kuat

            4. BENEFITS SECTION
            - Minimum 4 manfaat utama dalam format kartu/grid
            - Setiap manfaat: judul bold + deskripsi 1-2 kalimat
            - Fokus pada HASIL yang dirasakan user, bukan sekadar fitur

            5. FEATURES SECTION
            - List fitur dengan ikon atau bullet yang rapi
            - Setiap fitur diikuti penjelasan singkat manfaatnya

            6. SOCIAL PROOF SECTION
            - Buat 3 testimoni fiktif yang realistis dan spesifik (nama, jabatan, hasil nyata yang mereka rasakan)
            - Tambahkan placeholder untuk rating bintang

            7. PRICING SECTION
            - Tampilkan harga dengan anchor pricing jika memungkinkan
            - Sertakan apa saja yang didapat (value stack)
            - Tambahkan elemen urgensi atau scarcity

            8. CTA SECTION
            - Tombol CTA dengan teks yang action-oriented (bukan hanya 'Beli Sekarang')
            - Tambahkan micro-copy di bawah tombol (garansi, no risk, dll)
            - Ulangi CTA di akhir halaman

            ATURAN OUTPUT WAJIB:
            1. Output HANYA berupa HTML murni — tanpa tag <html>, <head>, <body>. Mulai langsung dari <div> atau <section>.
            2. DILARANG KERAS menggunakan markdown, backtick, atau blok kode apapun.
            3. Gunakan class Tailwind CSS untuk styling (assume Tailwind sudah ter-load).
            4. Gunakan palet warna profesional: dominan putih/abu-abu gelap, aksen biru profesional atau sesuaikan dengan karakter produk.
            5. Setiap section harus memiliki padding yang cukup dan visual hierarchy yang jelas.
            6. Gunakan emoji secara STRATEGIS dan MINIMAL hanya di bagian benefits atau features untuk visual cue — jangan berlebihan.
            7. Bahasa: Indonesia yang profesional, tegas, dan persuasif. Hindari bahasa yang terlalu formal atau kaku.
            8. PENTING: Tulis copy yang berbicara langsung ke pembaca menggunakan kata 'Anda' — bukan 'kamu' atau 'kami'.";

            // 4. Bagian menembak API Gemini
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=' . env('GEMINI_API_KEY'), [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal membuat konten.';

                // Simpan ke Database Supabase
                $salesPage = SalesPage::create([
                    'user_id' => Auth::id(),
                    'product_name' => $validated['product_name'],
                    'description' => $validated['description'],
                    'features' => $validated['features'] ?? null,
                    'target_audience' => $validated['target_audience'] ?? null,
                    'price' => $validated['price'] ?? null,
                    'unique_selling_points' => $validated['unique_selling_points'] ?? null,
                    'ai_generated_content' => trim($aiContent),
                ]);

                // ==========================================
                // 5. POTONG CREDIT USER SETELAH SUKSES
                // ==========================================
                $user->decrement('credits'); // Mengurangi 1 credit

                return response()->json([
                    'status' => 'success',
                    'message' => 'Sales page berhasil di-generate dan disimpan',
                    'sisa_credit' => $user->credits, // Frontend butuh info ini buat update UI
                    'data' => $salesPage
                ], 201);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi AI',
                'detail_error_google' => $response->json(),
                'status_code' => $response->status()
            ], $response->status());
        }

    // 3. Melihat detail 1 Sales Page berdasarkan ID (Read Detail)
    public function show($id)
    {
        $salesPage = SalesPage::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $salesPage]);
    }

    // 4. Menghapus riwayat (Delete)
    public function destroy($id)
    {
        $salesPage = SalesPage::where('user_id', Auth::id())->findOrFail($id);
        $salesPage->delete();

        return response()->json(['status' => 'success', 'message' => 'Riwayat berhasil dihapus']);
    }

    // Generate sales page secara streaming (Server-Sent Events) via Gemini.
    public function stream(Request $request, PromptBuilder $promptBuilder)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'target_audience' => 'nullable|string',
            'price' => 'nullable|string',
            'unique_selling_points' => 'nullable|string',
            'tone' => 'nullable|in:professional,casual,aggressive',
            'color_scheme' => 'nullable|in:blue,dark,green,custom',
            'custom_color' => 'nullable|string|max:30',
            'sections' => 'nullable|array',
            'image_url' => 'nullable|url',
            'logo_url' => 'nullable|url',
        ]);

        $user = Auth::user();
        if ($user->credits <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batas penggunaan API (35/35) telah habis untuk akun demo ini.',
            ], 403);
        }

        // Potong 1 credit di awal; akan di-refund jika generate gagal.
        $user->decrement('credits');
        $user->refresh();

        $prompt = $promptBuilder->build($validated);

        $response = new StreamedResponse(function () use ($prompt, $validated, $user) {
            $sendEvent = function (array $payload) {
                echo 'data: ' . json_encode($payload) . "\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                flush();
            };

            try {
                $apiResponse = Http::withoutVerifying()
                    ->timeout(120)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post(
                        'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:streamGenerateContent?alt=sse&key=' . env('GEMINI_API_KEY'),
                        ['contents' => [['parts' => [['text' => $prompt]]]]]
                    );

                if (! $apiResponse->successful()) {
                    $user->increment('credits'); // refund
                    $sendEvent(['error' => 'Gagal menghubungi AI. Credit Anda dikembalikan.']);
                    return;
                }

                // Parse body SSE Gemini ("data: {json}" per baris) dan teruskan teksnya.
                $full = '';
                foreach (preg_split('/\r?\n/', $apiResponse->body()) as $line) {
                    $line = trim($line);
                    if ($line === '' || ! str_starts_with($line, 'data:')) {
                        continue;
                    }
                    $json = trim(substr($line, 5));
                    if ($json === '[DONE]') {
                        continue;
                    }
                    $decoded = json_decode($json, true);
                    $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if ($text !== '') {
                        $full .= $text;
                        $sendEvent(['chunk' => $text]);
                    }
                }

                if (trim($full) === '') {
                    $user->increment('credits'); // refund: tidak ada konten
                    $sendEvent(['error' => 'AI tidak menghasilkan konten. Credit Anda dikembalikan.']);
                    return;
                }

                $salesPage = SalesPage::create([
                    'user_id' => $user->id,
                    'product_name' => $validated['product_name'],
                    'description' => $validated['description'],
                    'features' => $validated['features'] ?? null,
                    'target_audience' => $validated['target_audience'] ?? null,
                    'price' => $validated['price'] ?? null,
                    'unique_selling_points' => $validated['unique_selling_points'] ?? null,
                    'tone' => $validated['tone'] ?? null,
                    'color_scheme' => $validated['color_scheme'] ?? null,
                    'image_url' => $validated['image_url'] ?? null,
                    'logo_url' => $validated['logo_url'] ?? null,
                    'ai_generated_content' => trim($full),
                ]);

                $sendEvent([
                    'done' => true,
                    'id' => $salesPage->id,
                    'sisa_credit' => $user->fresh()->credits,
                ]);
            } catch (\Throwable $e) {
                $user->increment('credits'); // refund saat exception
                $sendEvent(['error' => 'Terjadi kesalahan saat generate. Credit Anda dikembalikan.']);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}
