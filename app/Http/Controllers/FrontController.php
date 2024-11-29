<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\FrontService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    protected $frontService;

    public function __construct(FrontService $frontService)
    {
        $this->frontService = $frontService;
    }

    //Konsep Services Repository Pettern
    public function index()
    {
        $data = $this->frontService->getFrontPageData();
        dd($data);
        // return view('front.index', $data);
    }

    public function details(Ticket $ticket)
    {
        dd($ticket);
        // return view('front.details', compact('ticket'));
    }

    public function category(Category $category)
    {
        dd($category);
        // return view('front.category', compact('category'));
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
