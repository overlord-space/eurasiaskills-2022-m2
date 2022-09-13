@extends('layout')

@section('content')
    <form
        class="w-[30rem] max-w-full flex flex-col gap-8 p-8 rounded-xl border border-gray-100 shadow-sm"
        method="post"
        enctype="multipart/form-data"
    >
        <div class="flex flex-row flex-wrap items-center">
            <h1 class="text-2xl text-left mr-auto">Project creation</h1>
            <a
                href="{{route('project.list')}}"
                class="px-4 py-2 text-sm text-center cursor-pointer rounded-xl bg-gray-400 bg-opacity-20 text-gray-700 transition-colors outline-none hover:bg-opacity-40"
            >
                &leftarrow;
                Back to list
            </a>
        </div>

        <label class="flex flex-col gap-2 text-gray-600">
            Project name
            <input
                class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                type="text"
                name="name"
                value="{{old('ssid', 'Example project')}}"
                required
            >
            @error('name')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        <label class="flex flex-col gap-2 text-gray-600">
            Wi-Fi SSID
            <input
                class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                type="text"
                name="ssid"
                value="{{old('ssid', 'example')}}"
                required
            >
            @error('ssid')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        <label class="flex flex-col gap-2 text-gray-600">
            Wi-Fi Password
            <input
                class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                type="password"
                name="ssid_password"
                value="{{old('ssid_password', 'example')}}"
                required
            >
            @error('ssid_password')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        <label class="flex flex-col gap-2 text-gray-600">
            Vendor modules
            <select
                class="px-5 h-[10rem] py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                name="vendor-module[]"
                multiple
            >
                @foreach($vendor_modules as $title => $category)
                    <optgroup label="[{{$title}}]">
                        @foreach($category as $key => $module)
                            <option value="{{$key}}">{{$module}}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('vendor-module')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        <label class="flex flex-col gap-2 text-gray-600">
            User modules
            <select
                class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                name="user-module[]"
                multiple
            >
                @foreach($user_modules as $module)
                    <option value="{{$module->id}}">{{$module->name}}</option>
                @endforeach
            </select>
            @error('user-module')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        @csrf

        <button
            class="py-4 text-center rounded-xl bg-green-400 bg-opacity-20 text-green-700 transition-colors outline-none hover:bg-opacity-40"
            type="submit"
        >
            Create
        </button>
    </form>
@endsection

