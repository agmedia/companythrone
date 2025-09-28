<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\Banner\StoreBannerRequest;
use App\Models\Back\Banners\Banner;
use App\Models\Back\Banners\BannerTranslation;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    protected function locales(): array
    {
        // iz settings tablice (ako postoji), inače fallback
        $list = \App\Models\Back\Settings\Settings::get('language','list');
        if ($list instanceof \Illuminate\Support\Collection && $list->count()) {
            return $list->mapWithKeys(fn($o) => [$o->code => $o->title->en ?? strtoupper($o->code)])->toArray();
        }
        return ['hr' => 'Croatian', 'en' => 'English'];
    }

    public function index(Request $request)
    {
        $q = Banner::query()->with(['translations' => function ($t) {
            $lc = function_exists('current_locale') ? current_locale() : app()->getLocale();
            $t->where('locale', $lc);
        }]);

        if ($s = $request->string('status')->toString()) {
            if (in_array($s, ['draft','active','archived'], true)) $q->where('status', $s);
        }
        if ($term = $request->string('q')->toString()) {
            $lc = function_exists('current_locale') ? current_locale() : app()->getLocale();
            $q->whereHas('translations', fn($t) => $t->where('locale',$lc)->where('title','like',"%{$term}%"));
        }

        $banners = $q->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $locales = $this->locales();
        return view('admin.banners.edit', compact('locales'));
    }

    public function store(StoreBannerRequest $request)
    {
        $banner = Banner::create(['status' => $request->string('status'), 'clicks' => 0]);

        $this->syncTranslations($banner, $request->input('tr', []));

        if ($request->hasFile('image')) {
            $banner->addMediaFromRequest('image')->toMediaCollection('banner');
        }

        return redirect()->route('admin.banners.show', $banner)->with('success', 'Banner created.');
    }

    public function show(Banner $banner)
    {
        $banner->load(['translations','schedules']);
        $locales = $this->locales();
        return view('admin.banners.show', compact('banner','locales'));
    }

    public function edit(Banner $banner)
    {
        $banner->load('translations');
        $locales = $this->locales();
        return view('admin.banners.edit', compact('banner','locales'));
    }

    public function update(StoreBannerRequest $request, Banner $banner)
    {
        $banner->update(['status' => $request->string('status')]);

        $this->syncTranslations($banner, $request->input('tr', []));

        if ($request->boolean('remove_image')) {
            $banner->clearMediaCollection('banner');
        } elseif ($request->hasFile('image')) {
            $banner->addMediaFromRequest('image')->toMediaCollection('banner');
        }

        return redirect()->route('banners.show', $banner)->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success','Banner deleted.');
    }

    protected function syncTranslations(Banner $banner, array $payload): void
    {
        foreach ($payload as $locale => $data) {
            $title  = $data['title']  ?? null;
            $slogan = $data['slogan'] ?? null;
            $url    = $data['url']    ?? null;

            BannerTranslation::updateOrCreate(
                ['banner_id' => $banner->id, 'locale' => $locale],
                ['title' => (string)$title, 'slogan' => $slogan, 'url' => $url]
            );
        }
    }


}
