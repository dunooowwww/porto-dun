<?php

namespace App\Http\Controllers;

use App\Support\FrontData;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Halaman depan one-page. Semua data dari database lewat FrontData (ter-cache).
     */
    public function __invoke(): View
    {
        return view('home', [
            'cards' => FrontData::pitWall(),
            'race' => FrontData::raceStrategy(),
            'projects' => FrontData::garage(),
            'mascot' => FrontData::mascot(),
        ]);
    }
}
