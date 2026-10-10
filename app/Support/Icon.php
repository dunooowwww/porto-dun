<?php

namespace App\Support;

use BladeUI\Icons\Factory;
use Illuminate\Support\HtmlString;

/**
 * Ikon garis Tabler (stroke 1.5) sesuai design.md. Dipakai lewat komponen <x-site.icon name="arrow-right" />.
 *
 * Paket ikon diam-diam mengganti nama yang salah dengan ikon "section", jadi keberadaan file dicek sendiri.
 */
class Icon
{
    public static function exists(?string $name): bool
    {
        if (blank($name) || ! preg_match('/^[a-z0-9-]+$/', $name)) {
            return false;
        }

        foreach (app(Factory::class)->all()['tabler']['paths'] ?? [] as $path) {
            if (is_file("{$path}/{$name}.svg")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ikon yang boleh dipilih di admin untuk konten (chip tech di hero).
     * Ikon "brand-*" adalah logo merek asli → dilarang design.md bagian 7.
     */
    public static function allowedForContent(?string $name): bool
    {
        return static::exists($name) && ! str_starts_with((string) $name, 'brand-');
    }

    public static function render(string $name, string $class = ''): HtmlString
    {
        if (! static::exists($name)) {
            return new HtmlString('');
        }

        $html = svg("tabler-{$name}", $class, [
            'stroke-width' => '1.5',
            'aria-hidden' => 'true',
            'focusable' => 'false',
        ])->toHtml();

        // Buang atribut bawaan file (stroke-width="2" dan class Tabler) supaya tidak dobel.
        $html = preg_replace('/\s(stroke-width="2"|class="icon icon-tabler[^"]*")/', '', $html);

        return new HtmlString($html);
    }
}
