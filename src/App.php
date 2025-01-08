<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

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
        $application = new Application(name: 'clibitica', version: '0.0.14');
        $application->setAutoExit($autoExit);

        $application->setCommandLoader($this->commandLoader());
        $application->setDefaultCommand('task:list');

        return $application->run($input, $output);
    }

    private function commandLoader(): FactoryCommandLoader
    {
        return new FactoryCommandLoader([
            'task:create' => fn () => new Command\Task\CreateCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'task:delete' => fn () => new Command\Task\DeleteCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'task:update' => fn () => new Command\Task\UpdateCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'task:list' => fn () => new Command\Task\ListCommand($this->mapper(), $this->habitica()),
            'task:info' => fn () => new Command\Task\InfoCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'task:score' => fn () => new Command\Task\ScoreCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'task:tag' => fn () => new Command\Task\TagCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'task:checklist' => fn () => new Command\Task\ChecklistCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),

            'tag:create' => fn () => new Command\Tag\CreateCommand($this->mapper(), $this->habitica()),
            'tag:delete' => fn () => new Command\Tag\DeleteCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'tag:update' => fn () => new Command\Tag\UpdateCommand(
                $this->mapper(),
                $this->habitica(),
                $this->suggestions(),
            ),
            'tag:list' => fn () => new Command\Tag\ListCommand($this->habitica()),

            'user:stats' => fn () => new Command\User\StatsCommand($this->habitica()),

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

    private ?Command\InputMapper\Mapper $mapper = null;

    private function mapper(): Command\InputMapper\Mapper
    {
        if (null === $this->mapper) {
            $this->mapper = new Command\InputMapper\Mapper(
                typeResolver: TypeResolver::create(),
                suggestions: $this->suggestions(),
            );
        }

        return $this->mapper;
    }

    private ?Habitica\Habitica $habitica = null;

    private function habitica(): Habitica\Habitica
    {
        if (null === $this->habitica) {
            $this->habitica = Habitica\Habitica::create(
                baseUrl: $this->config->habiticaBaseUrl,
                apiKey: $this->config->habiticaApiKey,
                apiUser: $this->config->habiticaApiUser,
                cacheDir: $this->config->cacheDirectory.'/habitica',
            );
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
            $this->wireMock = WireMock\WireMock::create($this->config->wireMockBaseUrl);
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
