<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class App
{
    public function __construct(private readonly Config $config)
    {
    }

    public function run(
        ?InputInterface $input = null,
        ?OutputInterface $output = null,
        bool $autoExit = true,
    ): int {
        $application = new Application(name: 'clibitica', version: '0.0.3');
        $application->setAutoExit($autoExit);

        $application->setCommandLoader($this->commandLoader());

        return $application->run($input, $output);
    }

    private function commandLoader(): FactoryCommandLoader
    {
        return new FactoryCommandLoader([
            'task:create' => fn () => new Command\Task\CreateCommand($this->habitica(), $this->suggestions()),
            'task:delete' => fn () => new Command\Task\DeleteCommand($this->habitica(), $this->suggestions()),
            'task:list' => fn () => new Command\Task\ListCommand($this->habitica(), $this->suggestions()),
            'task:score:up' => fn () => new Command\Task\ScoreUpCommand($this->habitica(), $this->suggestions()),
            'task:score:down' => fn () => new Command\Task\ScoreDownCommand($this->habitica(), $this->suggestions()),

            'tag:create' => fn () => new Command\Tag\CreateCommand($this->habitica()),
            'tag:delete' => fn () => new Command\Tag\DeleteCommand($this->habitica(), $this->suggestions()),
            'tag:list' => fn () => new Command\Tag\ListCommand($this->habitica()),

            'wiremock:reset' => fn () => new Command\WireMock\ResetCommand($this->wireMock(), !$this->config->dev),
            'wiremock:recording:start' => fn () => new Command\WireMock\Recording\StartCommand(
                $this->wireMock(),
                !$this->config->dev,
            ),
            'wiremock:recording:stop' => fn () => new Command\WireMock\Recording\StopCommand(
                $this->wireMock(),
                !$this->config->dev,
            ),
            'wiremock:mapping:fix' => fn () => new Command\WireMock\Mapping\FixCommand(
                $this->mappingFixer(),
                !$this->config->dev,
            ),
        ]);
    }

    private ?Habitica\Habitica $habitica = null;

    private function habitica(): Habitica\Habitica
    {
        if (null === $this->habitica) {
            $client = HttpClient::createForBaseUri($this->config->habiticaBaseUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Client' => '49de7a0b-cad8-4788-830b-8299c34e96a1 - clibitica',
                    'X-Api-Key' => $this->config->habiticaApiKey,
                    'X-Api-User' => $this->config->habiticaApiUser,
                ],
            ]);

            $serializer = new Serializer(
                normalizers: [
                    new ArrayDenormalizer(),
                    new DateTimeNormalizer(),
                    new BackedEnumNormalizer(),
                    new ObjectNormalizer(propertyTypeExtractor: new PhpDocExtractor()),
                ],
                encoders: [new JsonEncoder()],
            );

            $this->habitica = new Habitica\Habitica(new Http\Http($client, $serializer));
        }

        return $this->habitica;
    }

    private ?Command\Suggestions $suggestions = null;

    private function suggestions(): Command\Suggestions
    {
        if (null === $this->suggestions) {
            $this->suggestions = new Command\Suggestions($this->habitica());
        }

        return $this->suggestions;
    }

    private ?WireMock\WireMock $wireMock = null;

    private function wireMock(): WireMock\WireMock
    {
        if (null === $this->wireMock) {
            $client = HttpClient::createForBaseUri($this->config->wireMockBaseUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $serializer = new Serializer(
                normalizers: [],
                encoders: [new JsonEncoder()],
            );

            $this->wireMock = new WireMock\WireMock(new Http\Http($client, $serializer));
        }

        return $this->wireMock;
    }

    private ?WireMock\Mapping\Fixer $mappingFixer = null;

    private function mappingFixer(): WireMock\Mapping\Fixer
    {
        if (null === $this->mappingFixer) {
            $this->mappingFixer = new WireMock\Mapping\Fixer();
        }

        return $this->mappingFixer;
    }
}
