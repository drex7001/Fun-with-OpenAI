<?php

use App\AI\Assistant;
use App\AI\Chat;
use App\Rules\SpamFree;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use OpenAI\Laravel\Facades\OpenAI;

// Route::get('/', function () {
//     // session(['file' => 'sdfsfdsfdf.mp3']);
//     // session()->forget('file');
//     return view('roast');
// });

// Route::post('roast', function () {
//     set_time_limit(300);
//     $attributes = request()->validate([
//         "topic" => ['required','string','min:2', 'max:50'],
//     ]);

//     $mp3 = (new Chat())->send(
//         message : "Please roast {$attributes['topic']} in sarcastic tone",
//         speech : true,
//     );
//     // $file = "/roasts/".md5($mp3).".mp3"; //Linux
//     $file = "\\roasts\\" . md5($mp3) . ".mp3"; //Windows

//     file_put_contents(public_path($file), $mp3);
//     return redirect('/')->with([
//         'file' => $file,
//         'flash' => 'Boom. Roasted'
//     ]);
// });
Route::get('/', function(){
    return view('image',[
        'messages' => session('messages', []),
    ]);
});

Route::post('/image', function(){
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

Route::get('/spam', function(){
    return view('spam',[
        'messages' => session('messages', []),
    ]);
});

Route::post('/is_spam', function(){
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

Route::get('custom_assistant', function(){
    $file = OpenAI::files()->upload([
        'purpose' => 'assistants',
        'file' => fopen(storage_path('docs/parsing.md'),'rb')
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

    do{
        sleep(2);
        $run = OpenAI::threads()->runs()->retrieve(
            threadId:$run->threadId,
            runId: $run->id
        );
    } while($run->status != 'completed');

    $messages = OpenAI::threads()->messages()->list($run->threadId);

    dd($messages);
});
