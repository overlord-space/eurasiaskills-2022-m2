@extends('layout')

@section('content')
    <form method="post"
          class="w-[30rem] max-w-full flex flex-col gap-8 p-8 rounded-xl border border-gray-100 shadow-sm">
        <h1 class="text-3xl text-center">Registration</h1>

        <label class="flex flex-col gap-2">
            Email
            <input
                class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                type="email"
                value="{{old('email', 'test@user.com')}}"
                name="email"
                required
            >
            @error('email')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        <label class="flex flex-col gap-2">
            Password
            <input
                class="px-5 py-3 text-base border border-gray-200 rounded-xl outline-none transition-colors ring ring-transparent focus:ring-green-100"
                type="password"
                value="{{old('password', 'testtest')}}"
                name="password"
                required
            >
            @error('password')
            <p class="text-red-400">{{$message}}</p>
            @enderror
        </label>

        @csrf

        <button
            class="py-4 text-center rounded-xl bg-green-400 bg-opacity-20 text-green-700 transition-colors outline-none hover:bg-opacity-40"
            type="submit"
        >
            Register
        </button>

        <p class="text-sm">Already have account? <a class="transition-colors hover:text-blue-500" href="/login">Go to login</a></p>
    </form>
@endsection

