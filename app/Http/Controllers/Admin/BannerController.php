<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Back\Marketing\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => ['required','string','max:255'],
            'image'   => ['nullable','string','max:255'], // ako koristiš MediaLibrary, zamijeni
            'link'    => ['nullable','string','max:255'],
            'active'  => ['boolean'],
            'starts_at'=>['nullable','date'],
            'ends_at'  =>['nullable','date','after_or_equal:starts_at'],
        ]);

        Banner::create($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title'   => ['required','string','max:255'],
            'image'   => ['nullable','string','max:255'],
            'link'    => ['nullable','string','max:255'],
            'active'  => ['boolean'],
            'starts_at'=>['nullable','date'],
            'ends_at'  =>['nullable','date','after_or_equal:starts_at'],
        ]);

        $banner->update($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('success', 'Banner deleted.');
    }
}
