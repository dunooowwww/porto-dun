<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\SiteCache;
use Illuminate\Http\Response;

/**
 * sitemap.xml & robots.txt (PRD §9 SEO). Dibuat dinamis supaya URL-nya selalu sesuai domain.
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        // Yang di-cache hanya datanya; URL dibentuk per request supaya selalu sesuai domain yang dipakai.
        $projects = SiteCache::remember('seo.sitemap', fn () => Project::published()
            ->get(['slug', 'updated_at'])
            ->map(fn (Project $project) => ['slug' => $project->slug, 'lastmod' => $project->updated_at->toAtomString()])
            ->all());

        $xml = view('seo.sitemap', [
            'homeLastmod' => collect($projects)->max('lastmod') ?? now()->toAtomString(),
            'projects' => $projects,
        ])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
