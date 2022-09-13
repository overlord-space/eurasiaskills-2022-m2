<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>M2</title>

    <script src="/public/js/tailwindcss-cdn-3.1.8.js"></script>
</head>
<body class="p-4 min-h-screen flex flex-col flex-nowrap gap-5 justify-center items-center">
@if(session()->has('status'))
    <div class="w-[30rem] max-w-full border border-blue-100 bg-blue-100 bg-opacity-30 px-8 py-5 rounded-xl shadow-sm">
        @if(is_array(session('status')))
            @foreach(session('status') as $statusString)
                <p>{{$statusString}}</p>
            @endforeach
        @else
            {{session('status')}}
        @endif
    </div>
@endif
@yield('content')
@auth
    <div class="w-[30rem] max-w-full flex flex-row flex-nowrap gap-3 text-blue-500">
        <a class="text-sm transition-colors hover:text-blue-800" href="{{route('project.list')}}">Projects</a>
        <a class="text-sm transition-colors hover:text-blue-800" href="{{route('module.list')}}">Modules</a>
        <a class="text-sm transition-colors hover:text-blue-800" href="{{route('token.list')}}">API Tokens</a>
        <a class="ml-auto text-sm transition-colors hover:text-blue-800" href="{{route('logout')}}">Logout</a>
    </div>
@endauth
</body>
</html>
