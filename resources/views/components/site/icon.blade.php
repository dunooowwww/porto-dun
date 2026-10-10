@props(['name'])
{{ \App\Support\Icon::render($name, $attributes->get('class', '')) }}
