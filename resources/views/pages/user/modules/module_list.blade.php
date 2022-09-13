@extends('layout')

@section('content')
    <div class="w-[30rem] max-w-full flex flex-col gap-8 p-8 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-row flex-wrap items-center">
            <h1 class="text-2xl text-left mr-auto">Modules list</h1>
            <a
                href="{{route('module.add')}}"
                class="px-4 py-2 text-sm text-center cursor-pointer rounded-xl bg-green-400 bg-opacity-20 text-green-700 transition-colors outline-none hover:bg-opacity-40"
            >
                &plus;
                Upload modules
            </a>
        </div>

        <div class="flex flex-col flex-nowrap rounded-xl border border-gray-100 divide-y divide-gray-100">
            @forelse($modules as $module)
                <div class="flex flex-row flex-nowrap gap-2 p-4 items-center">
                    <div class="flex flex-col flex-nowrap mr-auto">
                        <p class="text-lg">{{$module->name}}</p>
                        <p class="text-sm text-gray-700">{{$module->desc}}</p>
                    </div>

                    <a
                        class="px-4 py-2 text-sm text-center cursor-pointer rounded-xl bg-green-400 bg-opacity-20 text-green-700 transition-colors outline-none hover:bg-opacity-40"
                        href="{{route('module.download', ['module' => $module->id])}}"
                    >
                        Download
                    </a>

                    <a
                        class="px-4 py-2 text-sm text-center cursor-pointer rounded-xl bg-red-400 bg-opacity-20 text-red-700 transition-colors outline-none hover:bg-opacity-40"
                        href="{{route('module.delete', ['module' => $module->id])}}"
                    >
                        Delete
                    </a>
                </div>
            @empty
                <p class="p-5 text-gray-700 italic text-center text-sm">Modules not found!</p>
            @endforelse
        </div>
    </div>
@endsection

