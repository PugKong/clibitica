<?php

declare(strict_types=1);

namespace App\WireMock;

use App\Http\Http;

final readonly class WireMock
{
    public function __construct(private Http $http)
    {
    }

    public function reset(): void
    {
        $this->http
            ->post('__admin/reset')
            ->fetch()
        ;
    }

    public function startRecording(): void
    {
        $this->http
            ->post('__admin/recordings/start')
            ->bodyJson([
                'targetBaseUrl' => 'https://habitica.com',
                'captureHeaders' => [
                    'Accept' => ['caseInsensitive' => true],
                    'Content-Type' => ['caseInsensitive' => true],
                    'X-Client' => ['caseInsensitive' => true],
                    'X-Api-Key' => ['caseInsensitive' => true],
                    'X-Api-User' => ['caseInsensitive' => true],
                ],
                'requestBodyPattern' => ['matcher' => 'equalToJso'],
                'repeatsAsScenarios' => false,
            ])
            ->fetch()
        ;
    }

    public function stopRecording(): void
    {
        $this->http
            ->post('__admin/recordings/stop')
            ->fetch()
        ;
    }

    public function listRequests(): Request\List\Response
    {
        return $this->http
            ->get('__admin/requests')
            ->fetchJson(Request\List\Response::class)
        ;
    }
}
