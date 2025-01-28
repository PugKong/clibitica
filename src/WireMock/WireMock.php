<?php

declare(strict_types=1);

namespace App\WireMock;

use Pugkong\Symfony\Requests\Request;
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
    public function __construct(private Request $request)
    {
    }

    public static function create(string $baseUrl): self
    {
        $client = HttpClient::create();

        $serializer = new Serializer(
            normalizers: [
                new ArrayDenormalizer(),
                new ObjectNormalizer(propertyTypeExtractor: new PhpDocExtractor()),
            ],
            encoders: [new JsonEncoder()],
        );

        $request = Request::create($client, $serializer)
            ->base($baseUrl)
            ->headers([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
        ;

        return new self($request);
    }

    public function reset(): void
    {
        $this->request
            ->post('__admin/reset')
            ->response()
            ->checkStatus(200)
        ;
    }

    public function startRecording(): void
    {
        $this->request
            ->post('__admin/recordings/start')
            ->body([
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
            ->response()
            ->checkStatus(200)
        ;
    }

    public function stopRecording(): void
    {
        $this->request
            ->post('__admin/recordings/stop')
            ->response()
            ->checkStatus(200)
        ;
    }

    public function listRequests(): Api\List\Response
    {
        return $this->request
            ->get('__admin/requests')
            ->response()
            ->checkStatus(200)
            ->object(Api\List\Response::class)
        ;
    }

    public function addMappingFromFile(string $filename): void
    {
        $content = file_get_contents($filename);
        if (false === $content) {
            throw new RuntimeException(sprintf('Failed to open "%s" file', $filename));
        }

        $this->request
            ->post('__admin/mappings')
            ->rawBody($content)
            ->response()
            ->checkStatus(201)
        ;
    }
}
