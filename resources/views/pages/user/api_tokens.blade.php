@extends('layout')

@section('content')
    <div class="w-[30rem] max-w-full flex flex-col gap-8 p-8 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-row flex-wrap items-center">
            <h1 class="text-2xl text-left mr-auto">Tokens list</h1>
        </div>

        <form class="p-4 border rounded-xl" method="post">
            @csrf

            <p class="text-base mb-2">Create new token</p>
            <div class="flex flex-row flex-nowrap items-end gap-4">
                <label class="flex flex-col gap-2 text-gray-600 flex-auto">
                    Token name
                    <input
                        class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                        type="text"
                        name="name"
                        value="{{old('token', 'Example token')}}"
                        required
                    >
                    @error('name')
                    <p class="text-red-400">{{$message}}</p>
                    @enderror
                </label>

                <button
                    type="submit"
                    class="px-4 py-2 h-[3.2rem] text-sm text-center cursor-pointer rounded-xl bg-green-400 bg-opacity-20 text-green-700 transition-colors outline-none hover:bg-opacity-40"
                >
                    Create
                </button>
            </div>
        </form>

        <div class="flex flex-col flex-nowrap rounded-xl border border-gray-100 divide-y divide-gray-100">
            @forelse($tokens as $token)
                <div class="flex flex-row flex-nowrap gap-2 p-4 items-center">
                    <div class="flex flex-col flex-nowrap mr-auto">
                        <p class="text-lg">{{$token->name}}</p>
                        <p class="text-sm text-gray-700 break-all select-all">{{$token->token}}</p>
                    </div>

                    <a
                        class="px-4 py-2 text-sm text-center cursor-pointer rounded-xl bg-red-400 bg-opacity-20 text-red-700 transition-colors outline-none hover:bg-opacity-40"
                        href="{{route('token.delete', ['token' => $token->id])}}"
                    >
                        Delete
                    </a>
                </div>
            @empty
                <p class="p-5 text-gray-700 italic text-center text-sm">Tokens not found!</p>
            @endforelse
        </div>
    </div>
@endsection

