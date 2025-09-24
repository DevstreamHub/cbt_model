<?php
// app/Http/Controllers/BackgroundImageController.php
namespace App\Http\Controllers;

use App\Models\BackgroundImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackgroundImageController extends Controller
{
    public function index()
    {
        $images = BackgroundImage::latest()->get();
        return view('admin.backgrounds.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'background' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'nullable|string|max:100',
        ]);

        $path = $request->file('background')->store('backgrounds', 'public');

        BackgroundImage::create([
            'name' => $request->name,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Background uploaded successfully.');
    }

    public function activate($id)
    {
        BackgroundImage::query()->update(['is_active' => false]);
        BackgroundImage::findOrFail($id)->update(['is_active' => true]);

        return back()->with('success', 'Background activated.');
    }

    public function destroy($id)
    {
        $bg = BackgroundImage::findOrFail($id);
        Storage::disk('public')->delete($bg->file_path);
        $bg->delete();

        return back()->with('success', 'Background deleted.');
    }
}
