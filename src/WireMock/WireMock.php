<?php

declare(strict_types=1);

namespace App\WireMock;

use App\Http\Http;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use function sprintf;

final readonly class WireMock
{
    public function __construct(private Http $http)
    {
    }

    public static function create(string $baseUrl): self
    {
        $client = HttpClient::createForBaseUri($baseUrl, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $serializer = new Serializer(
            normalizers: [
                new ArrayDenormalizer(),
                new ObjectNormalizer(propertyTypeExtractor: new PhpDocExtractor()),
            ],
            encoders: [new JsonEncoder()],
        );

        return new self(new Http($client, $serializer));
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
                'requestBodyPattern' => ['matcher' => 'equalToJson'],
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

    public function addMappingFromFile(string $filename): void
    {
        $content = file_get_contents($filename);
        if (false === $content) {
            throw new RuntimeException(sprintf('Failed to open "%s" file', $filename));
        }

        $this->http
            ->post('__admin/mappings')
            ->status(201)
            ->body($content)
            ->fetch()
        ;
    }
}
