@extends('layout')

@section('content')
    <form
        class="w-[30rem] max-w-full flex flex-col gap-8 p-8 rounded-xl border border-gray-100 shadow-sm"
        method="post"
        enctype="multipart/form-data"
    >
        <div class="flex flex-row flex-wrap items-center">
            <h1 class="text-2xl text-left mr-auto">Modules upload</h1>
            <a
                href="{{route('module.list')}}"
                class="px-4 py-2 text-sm text-center cursor-pointer rounded-xl bg-gray-400 bg-opacity-20 text-gray-700 transition-colors outline-none hover:bg-opacity-40"
            >
                &leftarrow;
                Back to list
            </a>
        </div>

        <label class="flex flex-col gap-2 text-gray-600">
            Provide zip-archive with modules
            <input
                class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                type="file"
                name="modules"
                required
                accept="application/zip"
            >
            @error('modules')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        @csrf

        <button
            class="py-4 text-center rounded-xl bg-green-400 bg-opacity-20 text-green-700 transition-colors outline-none hover:bg-opacity-40"
            type="submit"
        >
            Upload
        </button>
    </form>
@endsection

