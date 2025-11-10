<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressController extends Controller
{
    /**
     * Display user's addresses
     */
    public function index()
    {
        return Inertia::render('Profile/Addresses/Index', [
            'addresses' => [],
        ]);
    }
}
