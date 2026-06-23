<?php

namespace App\Services;

class PromptBuilder
{
    private const TONES = [
        'professional' => 'Gunakan gaya bahasa profesional, tegas, dan kredibel.',
        'casual' => 'Gunakan gaya bahasa santai, hangat, dan akrab seperti berbicara dengan teman.',
        'aggressive' => 'Gunakan gaya bahasa agresif dan penuh urgensi yang mendorong tindakan cepat.',
    ];

    private const COLORS = [
        'blue' => 'Palet warna dominan biru profesional dengan aksen putih dan abu-abu.',
        'dark' => 'Palet warna gelap (dark mode) dengan aksen terang yang kontras.',
        'green' => 'Palet warna hijau segar yang menekankan kepercayaan dan pertumbuhan.',
    ];

    private const OPTIONAL_SECTIONS = [
        'faq' => 'FAQ SECTION — 4-6 pertanyaan umum beserta jawaban yang meyakinkan.',
        'guarantee' => 'GUARANTEE SECTION — jaminan/garansi yang menghilangkan risiko pembelian.',
        'comparison' => 'COMPARISON SECTION — tabel perbandingan produk ini vs alternatif lain.',
        'countdown' => 'COUNTDOWN SECTION — placeholder timer hitung mundur untuk urgensi.',
    ];

    public function build(array $input): string
    {
        $featuresText = ! empty($input['features']) && is_array($input['features'])
            ? implode(', ', $input['features'])
            : 'Tidak disebutkan';

        $tone = self::TONES[$input['tone'] ?? 'professional'] ?? self::TONES['professional'];

        $scheme = $input['color_scheme'] ?? 'blue';
        if ($scheme === 'custom' && ! empty($input['custom_color'])) {
            $colorInstruction = 'Gunakan palet warna kustom dengan warna utama ' . $input['custom_color'] . '.';
        } else {
            $colorInstruction = self::COLORS[$scheme] ?? self::COLORS['blue'];
        }

        $optional = '';
        foreach (($input['sections'] ?? []) as $key => $enabled) {
            if ($enabled && isset(self::OPTIONAL_SECTIONS[$key])) {
                $optional .= "\n            - " . self::OPTIONAL_SECTIONS[$key];
            }
        }

        $imageInstruction = '';
        if (! empty($input['image_url'])) {
            $imageInstruction .= "\n            - Sisipkan gambar produk dengan <img src=\"" . $input['image_url'] . "\" class=\"...\"> di Hero atau Product section.";
        }
        if (! empty($input['logo_url'])) {
            $imageInstruction .= "\n            - Tempatkan logo brand dengan <img src=\"" . $input['logo_url'] . "\" class=\"...\"> di bagian header/navigasi atas.";
        }

        return "Anda adalah seorang World-Class Copywriter dan Conversion Rate Optimization (CRO) Expert dengan pengalaman 15 tahun yang juga ahli membangun website landing page yang menghasilkan jutaan dolar. Tugas Anda menciptakan sales page yang SANGAT persuasif, emosional, panjang, dan mendorong konversi tinggi.

            DATA PRODUK:
            - Nama Produk: " . ($input['product_name'] ?? '') . "
            - Deskripsi: " . ($input['description'] ?? '') . "
            - Fitur Utama: " . $featuresText . "
            - Target Audiens: " . ($input['target_audience'] ?? 'Umum') . "
            - Harga: " . ($input['price'] ?? 'Hubungi untuk harga') . "
            - Unique Selling Points: " . ($input['unique_selling_points'] ?? 'Tidak disebutkan') . "

            GAYA BAHASA:
            - " . $tone . "

            ARAHAN VISUAL:
            - " . $colorInstruction . $imageInstruction . "

            STRUKTUR WAJIB (ikuti urutan, tulis copy yang PANJANG dan MENDALAM di tiap section):
            1. HERO SECTION — headline power (maks 10 kata) + sub-headline.
            2. PROBLEM SECTION — 3-4 pain point spesifik dan empatik.
            3. SOLUTION SECTION — perkenalkan produk sebagai solusi, jelaskan keunggulannya.
            4. BENEFITS SECTION — minimal 4 manfaat dalam grid/kartu.
            5. FEATURES SECTION — list fitur + manfaat tiap fitur.
            6. SOCIAL PROOF SECTION — 3 testimoni fiktif realistis + rating bintang.
            7. PRICING SECTION — value stack + anchor pricing + urgensi.
            8. CTA SECTION — tombol action-oriented + micro-copy + CTA ulang." . $optional . "

            ATURAN OUTPUT WAJIB:
            1. Output HANYA HTML murni — tanpa <html>, <head>, <body>. Mulai langsung dari <div> atau <section>.
            2. DILARANG markdown, backtick, atau blok kode apa pun.
            3. Gunakan class Tailwind CSS (asumsikan Tailwind sudah ter-load).
            4. Setiap section padding cukup dan visual hierarchy jelas.
            5. Emoji strategis dan minimal hanya di benefits/features.
            6. Bahasa Indonesia profesional, gunakan sapaan 'Anda'.
            7. Tulis copy yang panjang dan kaya — minimal beberapa paragraf bermakna per section.";
    }
}
