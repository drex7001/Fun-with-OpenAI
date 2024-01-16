<?php

namespace App\AI;

use OpenAI\Laravel\Facades\OpenAI;

class Assistant
{
    protected $messages = [];

    public function __construct(array $messages = []){
        $this->messages = $messages;
    }

    public function systemMessage(string $message): static
    {
        $this->addMessage($message, 'system');

        return $this;
    }

    public function send(string $message, bool $speech = false): ?string
    {
        $this->addMessage($message);

        $response = OpenAI::chat()->create([
                "model" => "gpt-3.5-turbo-1106",
                "messages" => $this->messages

        ])->choices[0]->message->content;

        if($response) {
            $this->addMessage($message, 'assistant');
        }

        return $speech ? $this->speech($response) : $response;
    }

    function speech(string $message) : string {
        $mp3 = OpenAI::audio()->speech([
            'model' => 'tts-1',
            'input' => $message,
            'voice' => 'alloy'
        ]);
        return $mp3;
    }

    public function reply(string $message): ?string
    {
        return $this->send($message);
    }

    function visualize(string $description, array $options = []) : string {
        $this->addMessage($description);

        $description = collect($this->messages)->where('role', 'user')->pluck('content')->implode(' ');

        logger($description);

        $options = array_merge([
            'prompt' => $description,
            'model' => 'dall-e-2',
        ],$options);

        $url = OpenAI::images()->create($options)->data[0]->url;

        $this->addMessage($url,'assistant');

        return $url;
    }

    protected function addMessage(string $message, string $role = 'user'){
        $this->messages[] = [
            'role' => $role,
            'content' => $message,
        ];

        return $this;
    }

    public function messages()
    {
        return $this->messages;
    }

}
