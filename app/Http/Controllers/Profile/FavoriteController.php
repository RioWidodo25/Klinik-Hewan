<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    /**
     * Display user's favorite products
     */
    public function index()
    {
        return Inertia::render('Profile/Favorites/Index', [
            'favorites' => [],
        ]);
    }
}
