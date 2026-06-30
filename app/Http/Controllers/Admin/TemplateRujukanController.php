<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateRujukan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplateRujukanController extends Controller
{
    public function index(Request $request)
    {
        $query = TemplateRujukan::query();
        
        if ($request->search) {
            $query->where('nama_template', 'like', '%' . $request->search . '%')
                  ->orWhere('isi_template', 'like', '%' . $request->search . '%');
        }
        
        $templates = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Admin/TemplateRujukan/Index', [
            'templates' => $templates,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'isi_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        TemplateRujukan::create([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'is_active' => $request->is_active ?? true,
        ]);

        return back()->with('success', 'Template Rujukan berhasil ditambahkan.');
    }

    public function update(Request $request, TemplateRujukan $template_rujukan)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'isi_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $template_rujukan->update([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Template Rujukan berhasil diperbarui.');
    }

    public function destroy(TemplateRujukan $template_rujukan)
    {
        $template_rujukan->delete();
        return back()->with('success', 'Template Rujukan berhasil dihapus.');
    }
}
