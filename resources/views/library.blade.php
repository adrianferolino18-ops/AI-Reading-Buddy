@extends('app')

@section('title', 'Reading Room Collections')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 text-slate-100">
    
    <div class="mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-white/5 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>📖</span> Reading Room Collections
            </h1>
            <p class="text-sm text-slate-400 mt-1">Select an assignment module below or upload a custom document file to start parsing.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-xl">
            ⚠️ {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <div class="bg-slate-900/60 border border-white/10 rounded-2xl p-6 shadow-md sticky top-20">
            <h2 class="text-base font-bold text-slate-200 mb-2 flex items-center gap-2">
                📥 Upload New Document
            </h2>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">Add any standard study document PDF file here to parse out an instant reading module workspace.</p>
            
            <form action="{{ route('modules.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Module Title</label>
                    <input type="text" name="title" required placeholder="e.g., Chapter 3 History..." 
                           class="w-full bg-slate-950/60 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Select PDF File</label>
                    
                    <div x-data="{ fileName: '' }" class="relative border-2 border-dashed border-white/10 hover:border-indigo-500/40 rounded-xl p-4 transition text-center bg-black/10">
                        <input type="file" name="pdf_file" accept=".pdf" required 
                               @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        <span class="text-2xl block mb-1">📄</span>
                        
                        <template x-if="!fileName">
                            <div>
                                <span class="text-xs font-medium text-indigo-400 block">Click or Drag PDF file here</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Maximum file upload size limit: 12MB</span>
                            </div>
                        </template>
                        
                        <template x-if="fileName">
                            <div>
                                <span class="text-xs font-bold text-emerald-400 block truncate px-2" x-text="fileName"></span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Ready to parse document! ✨</span>
                            </div>
                        </template>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 rounded-xl transition shadow-md shadow-indigo-600/10">
                    Parse Document Engine →
                </button>
            </form>
        </div>

        <div class="lg:col-span-2">
            @if($modules->isEmpty())
                <div class="bg-slate-900 border border-white/10 rounded-2xl p-12 text-center">
                    <span class="text-4xl text-slate-500">📭</span>
                    <h3 class="text-base font-bold text-slate-200 mt-4">No Reading Rooms Found</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Use the file manager form to initialize your first custom document module collection setup!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($modules as $module)
                        <div class="bg-slate-900 border border-white/10 hover:border-indigo-500/20 rounded-2xl p-5 flex flex-col justify-between transition group shadow-sm">
                            <div>
                                <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/5">
                                    <span class="bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-[9px] font-semibold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                        Module #{{ $module->id }}
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-200 group-hover:text-indigo-400 transition mb-2 tracking-tight">
                                    {{ $module->title }}
                                </h3>
                                <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed mb-4">
                                    {{ $module->body_text }}
                                </p>
                            </div>
                            
                            <a href="{{ route('modules.show', $module->id) }}" 
                               class="w-full text-center bg-slate-950/60 border border-white/10 hover:border-indigo-500/30 text-slate-200 hover:text-white text-xs font-semibold py-2 rounded-xl transition">
                                Open Reading Room ➔
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection