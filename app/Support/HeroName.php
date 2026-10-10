<?php

namespace App\Support;

/**
 * Menentukan bagian sapaan hero yang diberi garis bawah merah + label #12, supaya nama
 * hanya tertulis sekali. Urutan prioritas:
 *   1. Teks yang diapit bintang: "Halo, kenalin aku *Ardan*"
 *   2. Nama tampil (display_name) yang tertulis di dalam sapaan
 *   3. Kata terakhir sapaan
 * Jika sapaan kosong, nama tampil yang dipakai.
 */
class HeroName
{
    /**
     * @return array{before: string, name: string, after: string}
     */
    public static function split(?string $greeting, ?string $displayName): array
    {
        $greeting = trim((string) $greeting);
        $displayName = trim((string) $displayName);

        if ($greeting === '') {
            return ['before' => '', 'name' => $displayName, 'after' => ''];
        }

        if (preg_match('/^(.*?)\*([^*]+)\*(.*)$/u', $greeting, $m)) {
            return ['before' => $m[1], 'name' => trim($m[2]), 'after' => str_replace('*', '', $m[3])];
        }

        if ($displayName !== '' && ($pos = mb_stripos($greeting, $displayName)) !== false) {
            return [
                'before' => mb_substr($greeting, 0, $pos),
                'name' => mb_substr($greeting, $pos, mb_strlen($displayName)),
                'after' => mb_substr($greeting, $pos + mb_strlen($displayName)),
            ];
        }

        // Kata terakhir; tanda baca di ujung (mis. "!") tetap di luar garis.
        if (preg_match('/^(.*\s)?(\S+?)([.,!?…]*)$/u', $greeting, $m)) {
            return ['before' => $m[1] ?? '', 'name' => $m[2], 'after' => $m[3]];
        }

        return ['before' => '', 'name' => $greeting, 'after' => ''];
    }
}
