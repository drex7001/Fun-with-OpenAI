<!DOCTYPE html>
<html lang="en">
<head class="h-full">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>
<body class="h-full grid place-items-center p-6">
    <div class="text-xs font-sans">
        {!! nl2br($poem) !!}
    </div>
</body>
</html>
