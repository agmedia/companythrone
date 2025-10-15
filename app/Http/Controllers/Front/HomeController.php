<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Recaptcha;
use App\Http\Controllers\Controller;
use App\Mail\ContactFormMessage;
use App\Models\Back\Banners\Banner;
use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{

    public function index()
    {
        $featured = Company::query()
                           ->where('is_published', true)
                           ->latest()
                           ->take(12)
                           ->get();

        $cats = Category::defaultOrder()->get()->toTree();

        $locale = app()->getLocale();

        // Bannere vuci samo ACTIVE + prijevod za aktivni jezik
        $banners = Banner::query()
                         ->where('status', 'active')
                         ->whereHas('translations', fn(Builder $q) => $q->where('locale', $locale))
                         ->with(['translations' => fn($q) => $q->where('locale', $locale)])
                         ->latest()
                         ->take(12) // prilagodi koliko slajdova želiš
                         ->get();

        // 🔍 Provjeri ima li logirani user svoju company
        $user = auth()->user();
        $hasCompany = false;

        if ($user) {
            $hasCompany = Company::where('user_id', $user->id)->exists();
        }

        return view('front.home', compact('featured', 'cats', 'banners', 'hasCompany'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function contact(Request $request)
    {
        return view('front.contact');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function faq(Request $request)
    {
        return view('front.faq');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function sendContactMessage(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required|email',
            'message' => 'required',
        ]);

        // Recaptcha
        if (!recaptcha_ok('contact_form')) {
            return back()->withErrors(['error' => 'ReCaptcha provjera nije prošla.']);
        }

        Mail::to(config('mail.from.address'))
            ->send(new ContactFormMail(
                name: $request->string('name')->toString(),
                email: $request->string('email')->toString(),
                subject: $request->string('subject')->toString() ?? null,
                messageText: $request->string('message')->toString()
            ));

        return view('front.contact')->with(['success' => 'Vaša poruka je uspješno poslana.! Odgovoriti ćemo vam uskoro.']);
    }

}
