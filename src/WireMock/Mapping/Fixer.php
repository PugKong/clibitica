<?php

declare(strict_types=1);

namespace App\WireMock\Mapping;

use Webmozart\Assert\Assert;

use function array_key_exists;
use function is_string;
use function strlen;

use const ARRAY_FILTER_USE_KEY;
use const JSON_THROW_ON_ERROR;

final readonly class Fixer
{
    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    public function fix(string $mappingsDir, string $filename, array $config): array
    {
        $config['name'] = $this->name($mappingsDir, $filename);

        Assert::keyExists($config, 'request');
        Assert::isMap($config['request']);
        $config['request'] = $this->request($config['request']);

        Assert::keyExists($config, 'response');
        Assert::isMap($config['response']);
        $config['response'] = $this->response($config['response']);

        unset($config['persistent']);
        unset($config['insertionIndex']);

        return $config;
    }

    private function name(string $mappingsDir, string $filename): string
    {
        if (!str_ends_with($mappingsDir, '/')) {
            $mappingsDir .= '/';
        }

        return substr(
            string: $filename,
            offset: strlen($mappingsDir),
            length: strlen($filename) - strlen($mappingsDir) - strlen('.json'),
        );
    }

    /**
     * @param array<string, mixed> $request
     *
     * @return array<string, mixed>
     */
    private function request(array $request): array
    {
        if (array_key_exists('headers', $request)) {
            Assert::isMap($request['headers']);

            if (array_key_exists('X-Api-Key', $request['headers'])) {
                Assert::isMap($request['headers']['X-Api-Key']);

                if (array_key_exists('equalTo', $request['headers']['X-Api-Key'])) {
                    $request['headers']['X-Api-Key']['equalTo'] = 'api key';
                }
            }
        }

        if (array_key_exists('bodyPatterns', $request)) {
            Assert::isArray($request['bodyPatterns']);

            foreach ($request['bodyPatterns'] as $index => $bodyPattern) {
                if (array_key_exists('equalToJson', $bodyPattern) && is_string($bodyPattern['equalToJson'])) {
                    $request['bodyPatterns'][$index]['equalToJson'] = $this->decode($bodyPattern['equalToJson']);
                }
            }
        }

        return $request;
    }

    /**
     * @param array<string, mixed> $response
     *
     * @return array<string, mixed>
     */
    private function response(array $response): array
    {
        if (array_key_exists('headers', $response)) {
            Assert::isArray($response['headers']);

            $response['headers'] = array_filter(
                $response['headers'],
                fn ($key) => 'Content-Type' === $key,
                ARRAY_FILTER_USE_KEY,
            );
        }

        if (array_key_exists('body', $response)) {
            Assert::string($response['body']);

            $response['jsonBody'] = $this->decode($response['body']);
            unset($response['body']);
        }

        return $response;
    }

    private function decode(string $json): mixed
    {
        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }
}
