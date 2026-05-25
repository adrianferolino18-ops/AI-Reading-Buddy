<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use Smalot\PdfParser\Parser;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::orderBy('created_at', 'desc')->get();
        return view('library', compact('modules'));
    }

    public function show(Module $module)
    {
        return view('read', compact('module'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf,txt|max:12288',
        ]);

        $textPayload = "";

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            
            // Handle plain text files
            if ($file->getClientOriginalExtension() === 'txt') {
                $textPayload = file_get_contents($file->getRealPath());
            } 
            // Handle actual PDF documents using the composer engine parser package
            else if ($file->getClientOriginalExtension() === 'pdf') {
                try {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($file->getRealPath());
                    $textPayload = $pdf->getText();
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors([
                        'error' => 'Failed to parse the PDF file structure: ' . $e->getMessage()
                    ]);
                }
            }
        }

        if (empty(trim($textPayload))) {
            $textPayload = "Document source textual body is currently unreadable or empty.";
        }

        Module::create([
            'title' => $request->input('title'),
            'body_text' => $textPayload
        ]);

        return redirect()->back()->with('success', 'Reading workspace module processed successfully.');
    }

    public function destroy(Module $module)
    {
        try {
            // 💡 Cascade delete: wipes out all vocabulary words linked to this specific module first
            $module->savedWords()->delete();
            
            // Delete the core module entry
            $module->delete();

            return redirect()->route('modules.index')->with('success', 'Reading workspace module and related vocabulary successfully purged.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while deleting the module: ' . $e->getMessage()
            ]);
        }
    }
}