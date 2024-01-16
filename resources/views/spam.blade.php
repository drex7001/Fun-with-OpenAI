<!DOCTYPE html>
<html lang="en">

<head class="h-full">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body class="p-6 bg-slate-100 max-w-xl mx-auto">
    <div class="flex gap-6 mx-auto max-w-3xl bg-white py-6 px-10 rounded-xl">

        <form method="POST" action="/is_spam">
            @csrf

            <div class="space-y-12">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Create Reply</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">This will displayed publicly so be carefull what you
                        type</p>

                    <div class="mt-10 grid grid-col-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="col-span-full">
                            <label for="body" class="block text-sm font-medium leading-6 text-gray-900">Body</label>
                            <div class="mt-2">
                                <textarea name="body" id="body" rows="3"
                                    class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-800"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="mt-6 flex items-center justify-end gap-x-6">
                <button type="submit" class="text-sm font-semibold leading-6 text-gray-900">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 hover:text-white">
                    Submit
                </button>
            </div>
            @if ($errors->any())
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm text-red-500"> {{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </form>
        <div>

        </div>
    </div>
</body>

</html>
