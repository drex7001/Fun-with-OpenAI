<?php

use App\AI\Assistant;
use App\AI\Chat;
use App\Rules\SpamFree;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\LazyCollection;
use Illuminate\Validation\ValidationException;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('/roast', function () {
    // session(['file' => 'sdfsfdsfdf.mp3']);
    // session()->forget('file');
    return view('roast');
});

Route::post('roast', function () {
    set_time_limit(300);
    $attributes = request()->validate([
        "topic" => ['nullable','string','min:2', 'max:255'],
    ]);
    set_time_limit(0);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, 5000); // Set timeout for the cURL request (in seconds)



    $mp3 = (new Chat())->send(
        // message : "Please roast {$attributes['topic']} in sarcastic tone",
        message : "My lecture ask me to do video about chronology of AI. Give me a script for chronology of AI. I need just for 1 minutes content",
        speech : true,
    );
    // $file = "/roasts/".md5($mp3).".mp3"; //Linux
    $file = "\\roasts\\" . md5($mp3) . ".mp3"; //Windows

    file_put_contents(public_path($file), $mp3);
    return redirect('/')->with([
        'file' => $file,
        'flash' => 'Boom. Roasted'
    ]);
});
Route::get('/', function () {
    return view('image', [
        'messages' => session('messages', []),
    ]);
});

Route::post('/image', function () {
    $attributes = request()->validate([
        'description' => ['required', 'string','min:3' ]
    ]);

    $assistant = new Assistant(session('messages', []));

    $assistant->visualize($attributes['description']);

    session([
        'messages' => $assistant->messages()
    ]);
    return redirect('/');
});

Route::get('/spam', function () {
    return view('spam', [
        'messages' => session('messages', []),
    ]);
});

Route::post('/is_spam', function () {
    request()->validate([
        'body' => [
            'required',
            'string',
            'min:3',
            new SpamFree()
        ]
    ]);


    return 'Redirect whenever is needed. Post was valid';
});

Route::get('custom_assistant', function () {
    $file = OpenAI::files()->upload([
        'purpose' => 'assistants',
        'file' => fopen(storage_path('docs/parsing.md'), 'rb')
    ]);
    $assistants = OpenAI::assistants()->create([
        "model" => "gpt-4-1106-preview",
        "name" => "Laraparse Tutor",
        "instructions" => "You are a helpful programming teacher",
        "tools" => [
            ["type" => "retrieval"]
        ],
        'file_ids' => [
            $file->id
        ]
    ]);

    $run = OpenAI::threads()->createAndRun([
        'assistant_id' => $assistants->id,
        'thread' => [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'How do I grab the first paragraph'
                ]
            ],
        ]
    ]);

    do {
        sleep(2);
        $run = OpenAI::threads()->runs()->retrieve(
            threadId:$run->threadId,
            runId: $run->id
        );
    } while($run->status != 'completed');

    $messages = OpenAI::threads()->messages()->list($run->threadId);

    dd($messages);
});


Route::get('/lazy', function () {
    // function happyFunction(){
    //     dump(1);
    //     yield 'one';
    //     dump(2);

    //     dump(3);
    //     yield 'two';
    //     dump(4);

    //     dump(5);
    //     yield 'three';
    //     dump(6);
    // }

    // return get_class_methods(happyFunction());
    // ["rewind","valid","current","key","next","send","throw","getReturn"]

    // $return = happyFunction();
    // dump($return->current());
    // $return->next();
    // dump($return->current());
    // $return->next();
    // dump($return->current());
    // $return->next();
    // dump($return->current());

    // foreach(happyFunction() as $results){
    //     if($results == 'two'){
    //         return;
    //     }
    //     dump($results);
    // }

    $startMemory = memory_get_usage();
    $startPeakMemory = memory_get_peak_usage();

    $generator = function ($limit) {
        for ($number = 0; $number < $limit; $number++) {
            yield hash('sha256', pow(2, $number));
        }
    };

    $limit = 100000;
    $generator = $generator($limit);

    // Open a file handle for writing
    $file = fopen('output.csv', 'w');

    // Write header if needed
    fputcsv($file, ['Hash Value']);

    // Iterate over the generator and write to the CSV file
    foreach ($generator as $value) {
        fputcsv($file, [$value]);
    }

    // Close the file handle
    fclose($file);

    // return 'CSV file created successfully.';
    $endMemory = memory_get_usage();
    $endPeakMemory = memory_get_peak_usage();

    $memoryIncrease = ($endMemory - $startMemory) / 1024 / 1024; // Convert bytes to megabytes
    $peakMemoryIncrease = ($endPeakMemory - $startPeakMemory) / 1024 / 1024; // Convert bytes to megabytes

    return "Memory usage increased by: {$memoryIncrease} bytes (Peak: {$peakMemoryIncrease} bytes)";
});



Route::get('/lazy2', function () {
    $startMemory = memory_get_usage();
    $startPeakMemory = memory_get_peak_usage();
    $limit = 100000;
    $hashValues = [];

    for ($number = 0; $number < $limit; $number++) {
        $hashValues[] = hash('sha256', pow(2, $number));
    }

    // Get the path to the storage directory
    $storagePath = storage_path();

    // Define the file path within the storage directory
    $filePath = 'output.csv';

    // Open a file handle for writing
    $file = fopen($filePath, 'w');

    // Write header if needed
    fputcsv($file, ['Hash Value']);

    // Iterate over the array and write to the CSV file
    foreach ($hashValues as $value) {
        fputcsv($file, [$value]);
    }

    // Close the file handle
    fclose($file);
    $endMemory = memory_get_usage();
    $endPeakMemory = memory_get_peak_usage();

    $memoryIncrease = ($endMemory - $startMemory) / 1024 / 1024; // Convert bytes to megabytes
    $peakMemoryIncrease = ($endPeakMemory - $startPeakMemory) / 1024 / 1024; // Convert bytes to megabytes

    return "Memory usage increased by: {$memoryIncrease} bytes (Peak: {$peakMemoryIncrease} bytes)";
    // return 'CSV file created successfully at ' . $filePath;
});
