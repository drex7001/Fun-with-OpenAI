<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body class="h-full grid place-items-center p-6">
    @if (session('file'))
    {{-- @dump(session('file')) --}}
        <div>
            <img src="/storage/images/roasted.gif"/>
            <a href="{{ asset(session('file')) }}" download class="mt-2 block w-full text-center bg-gray-200 hover:bg-blue-500 hover:text-white"> Download Audio </a>
        </div>
    @else
        <form action="/roast" method="POST" class="w-full lg:max-w-md lg:mx-auto">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="topic" placeholder="What do you want us to roast?" required
                    class="border p-2 rounded flex-1">
                <button type="submit" class="rounded p-2 bg-gray-200 hover:bg-blue-500 hover:text-white">Roast</button>
            </div>
        </form>
    @endif

</body>

</html>
