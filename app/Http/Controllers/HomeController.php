<?php

namespace App\Http\Controllers;

use App\Models\ContentLink;
use App\Models\Label;
use App\Models\Referrer;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        $labels = Label::all();
        $users = User::all();
        $links = ContentLink::all();
        $refs = Referrer::all();
        return view('home', ['labels' => $labels, 'users' => $users, 'links' => $links, 'referrers' => $refs]);
    }

    public function updateLabels(Request $request): RedirectResponse
    {
        foreach ($request->labels as $label => $alias) {
            Label::query()
                ->updateOrCreate(['label' => $label], ['alias' => $alias]);
        }
        return redirect()->route('home');
    }

    public function updateLinks(Request $request): RedirectResponse
    {
        foreach ($request->links as $link) {
            ContentLink::query()
                ->updateOrCreate(['link' => $link]);
        }
        return redirect()->route('home');
    }

    public function updateReferrers(Request $request): RedirectResponse
    {
        foreach ($request->referrers as $link) {
            Referrer::query()->latest()
                ->update(['link' => $link]);
        }
        return redirect()->route('home');
    }

}
