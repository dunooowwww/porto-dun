<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Support\FrontData;
use Illuminate\Contracts\View\View;

class ProjectController extends Controller
{
    /**
     * Detail project / case study (PRD F7). Slug salah → 404.
     * Project draft hanya bisa dibuka admin yang sedang login (tombol "Preview" di admin).
     */
    public function show(string $slug): View
    {
        $project = Project::with('images')->where('slug', $slug)->firstOrFail();
        $isDraft = $project->status !== ProjectStatus::Published;

        abort_if($isDraft && ! auth()->check(), 404);

        // Posisi & navigasi sebelumnya/berikutnya mengikuti urutan Garage (hanya project terbit).
        $garage = FrontData::garage();
        $index = collect($garage)->search(fn (array $item) => $item['slug'] === $project->slug);

        return view('projects.show', [
            'project' => $project,
            'isDraft' => $isDraft,
            'position' => $index === false ? null : $index + 1,
            'previous' => $index !== false && $index > 0 ? $garage[$index - 1] : null,
            'next' => $index !== false ? ($garage[$index + 1] ?? null) : null,
        ]);
    }
}
