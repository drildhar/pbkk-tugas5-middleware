<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Area publik: halaman landing "Secure Academic Portal ITS".
     *
     * Dapat diakses siapa saja (guest) tanpa login.
     */
    public function index(): View
    {
        return view('welcome');
    }
}
