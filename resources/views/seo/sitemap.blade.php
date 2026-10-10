<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <lastmod>{{ $homeLastmod }}</lastmod>
    </url>
@foreach ($projects as $project)
    <url>
        <loc>{{ route('projects.show', $project['slug']) }}</loc>
        <lastmod>{{ $project['lastmod'] }}</lastmod>
    </url>
@endforeach
</urlset>
