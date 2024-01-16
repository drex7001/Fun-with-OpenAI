<?php

namespace App\AI;

use OpenAI\Laravel\Facades\OpenAI;

class Chat
{
    protected $messages = [];

    public function systemMessage(string $message): static
    {
        $this->messages[] = [
            'role' => 'system',
            'content' => $message,
        ];

        return $this;
    }

    public function send(string $message, bool $speech = false): ?string
    {
        $this->messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        $response = OpenAI::chat()->create([
                "model" => "gpt-3.5-turbo",
                "messages" => $this->messages

        ])->choices[0]->message->content;

        if($response) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response,
            ];
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

    public function messages()
    {
        return $this->messages;
    }

}
