<?php

declare(strict_types=1);

namespace App\Http;

use RuntimeException;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function sprintf;

use const PHP_EOL;

final class StatusCodeException extends RuntimeException
{
    public function __construct(int $expectedStatus, ResponseInterface $response)
    {
        /** @var string $method */
        $method = $response->getInfo('http_method');
        /** @var string $url */
        $url = $response->getInfo('url');

        $message = [];
        $message[] = sprintf(
            '%d return for %s %s, expected %d',
            $response->getStatusCode(),
            $method,
            $url,
            $expectedStatus,
        );
        $message[] = sprintf('response body: %s', $response->getContent(false));

        parent::__construct(implode(PHP_EOL, $message));
    }
}
