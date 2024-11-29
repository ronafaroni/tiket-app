<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\Request;

class FrontController extends Controller
{

    //Konsep Services Repository Pettern
    public function index()
    {
        return view('front.index');
    }

    //Konsep Menggunakan MVC
    // public function index()
    // {
    //     $category = Category::latest()->get();
    //     $popularTicket = Ticket::where('is_popular', true)->take(4)->get();
    //     $newTicket = Ticket::latest()->get();

    //     return view('front.index2', compact('category', 'popularTicket', 'newTicket'));
    // }
}
