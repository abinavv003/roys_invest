<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::orderBy('display_order')->orderByDesc('created_at')->get();
        return view('admin.gallery.index', compact('photos'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        GalleryPhoto::create([
            'title' => $validated['title'] ?? null,
            'image_path' => $path,
            'display_order' => $validated['display_order'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.gallery.index')->with('success', 'Photo added');
    }

    public function edit(GalleryPhoto $gallery)
    {
        return view('admin.gallery.edit', ['photo' => $gallery]);
    }

    public function update(Request $request, GalleryPhoto $gallery)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            $gallery->image_path = $request->file('image')->store('gallery', 'public');
        }

        $gallery->title = $validated['title'] ?? null;
        $gallery->display_order = $validated['display_order'] ?? null;
        $gallery->is_active = $request->boolean('is_active', true);
        $gallery->save();

        return redirect()->route('admin.gallery.index')->with('success', 'Photo updated');
    }

    public function destroy(GalleryPhoto $gallery)
    {
        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $gallery->delete();
        return redirect()->route('admin.gallery.index')->with('success', 'Photo deleted');
    }
}