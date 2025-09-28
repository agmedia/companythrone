<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Catalog\Category;
use App\Models\Admin\Catalog\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class CategoryController extends Controller
{
    protected array $allowedGroups = ['categories','blog','pages','footer'];

    public function index(Request $request)
    {
        $group = $this->normalizeGroup($request->string('group', 'categories'));

        $categories = Category::with([
            'translations',
            'media',
            'parent.translations',
            'children.translations',
            'children.media',
        ])
            ->forGroup($group)        // where('group', $group)->defaultOrder()
            ->defaultOrder()
            ->get();

        return view('admin.catalog.categories.index', compact('categories', 'group'));
    }

    public function create(Request $request)
    {
        $group   = $this->normalizeGroup($request->string('group', 'companies'));
        $parents = Category::with('translations')
            ->where('group', $group)
            ->defaultOrder()
            ->get();

        $category = new Category(['group' => $group, 'is_active' => true]);

        return view('admin.catalog.categories.create', compact('category', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group'        => ['required', 'in:companies,blog,pages,footer'],
            'parent_id'    => ['nullable', 'exists:categories,id'],
            'position'     => ['nullable', 'integer'],
            'is_active'    => ['nullable', 'boolean'],

            // translated (current locale)
            'name'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'seo_title'        => ['nullable', 'string', 'max:255'],
            'seo_description'  => ['nullable', 'string', 'max:255'],
            'seo_keywords'     => ['nullable', 'string', 'max:255'],

            // files
            'image_file'  => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'icon_file'   => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'banner_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
        ]);

        $category = DB::transaction(function () use ($request, $data) {
            $category = Category::create([
                'group'     => $data['group'],
                'parent_id' => $data['parent_id'] ?? null,
                'position'  => $data['position'] ?? 0,
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            $locale = app()->getLocale();

            CategoryTranslation::updateOrCreate(
                ['category_id' => $category->id, 'locale' => $locale],
                [
                    'name'           => $data['name'],
                    'slug'            => $data['slug'] ?? null,
                    'description'     => $data['description'] ?? null,
                    'seo_title'       => $data['seo_title'] ?? null,
                    'seo_description' => $data['seo_description'] ?? null,
                    'seo_keywords'    => $data['seo_keywords'] ?? null,
                    'seo_json'        => null,
                    'link_url'        => null,
                ]
            );

            // Media (replace if uploaded)
            if ($request->hasFile('image_file')) {
                $category->clearMediaCollection('image');
                $category->addMediaFromRequest('image_file')->toMediaCollection('image');
            }
            if ($request->hasFile('icon_file')) {
                $category->clearMediaCollection('icon');
                $category->addMediaFromRequest('icon_file')->toMediaCollection('icon');
            }
            if ($request->hasFile('banner_file')) {
                $category->clearMediaCollection('banner');
                $category->addMediaFromRequest('banner_file')->toMediaCollection('banner');
            }

            return $category;
        });

        // ✅ preserve active tab on redirect
        return redirect()->route('catalog.categories.index', ['group' => $category->group])
            ->with('success', __('back/categories.flash.created'));
    }

    public function edit(Category $category)
    {
        $group   = $this->normalizeGroup(request()->string('group', $category->group));
        $parents = Category::with('translations')
            ->where('group', $group)
            ->whereKeyNot($category->id)
            ->defaultOrder()
            ->get();

        return view('admin.catalog.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'group' => ['required','in:companies,blog,pages,footer'],
            'parent_id'    => ['nullable', 'exists:categories,id'],
            'position'     => ['nullable', 'integer'],
            'is_active'    => ['nullable', 'boolean'],

            // translated (current locale)
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'seo_title'        => ['nullable', 'string', 'max:255'],
            'seo_description'  => ['nullable', 'string', 'max:255'],
            'seo_keywords'     => ['nullable', 'string', 'max:255'],

            // files + remove toggles
            'image_file'   => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'icon_file'    => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'banner_file'  => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'remove_image' => ['nullable', 'boolean'],
            'remove_icon'  => ['nullable', 'boolean'],
            'remove_banner'=> ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $category, $data) {
            $category->update([
                'group'     => $data['group'],
                'parent_id' => $data['parent_id'] ?? null,
                'position'  => $data['position'] ?? 0,
                'is_active' => (bool)($data['is_active'] ?? false),
            ]);

            $locale = app()->getLocale();

            CategoryTranslation::updateOrCreate(
                ['category_id' => $category->id, 'locale' => $locale],
                [
                    'name'           => $data['name'],
                    'slug'            => $data['slug'] ?? null,
                    'description'     => $data['description'] ?? null,
                    'seo_title'       => $data['seo_title'] ?? null,
                    'seo_description' => $data['seo_description'] ?? null,
                    'seo_keywords'    => $data['seo_keywords'] ?? null,
                ]
            );

            // remove/replace media
            if ($request->boolean('remove_image')) {
                $category->clearMediaCollection('image');
            }
            if ($request->hasFile('image_file')) {
                $category->clearMediaCollection('image');
                $category->addMediaFromRequest('image_file')->toMediaCollection('image');
            }

            if ($request->boolean('remove_icon')) {
                $category->clearMediaCollection('icon');
            }
            if ($request->hasFile('icon_file')) {
                $category->clearMediaCollection('icon');
                $category->addMediaFromRequest('icon_file')->toMediaCollection('icon');
            }

            if ($request->boolean('remove_banner')) {
                $category->clearMediaCollection('banner');
            }
            if ($request->hasFile('banner_file')) {
                $category->clearMediaCollection('banner');
                $category->addMediaFromRequest('banner_file')->toMediaCollection('banner');
            }
        });

        // ✅ preserve active tab on redirect (after possible group change)
        return redirect()->route('catalog.categories.index', ['group' => $category->group])
            ->with('success', __('back/categories.flash.updated'));
    }

    public function destroy(Category $category)
    {
        $group = $category->group; // keep before delete
        $category->delete();

        // ✅ keep tab
        return redirect()->route('catalog.categories.index', ['group' => $group])
            ->with('success', __('back/categories.flash.deleted'));
    }

    private function normalizeGroup(string $group): string
    {
        return in_array($group, $this->allowedGroups, true) ? $group : 'categories';
    }
}
