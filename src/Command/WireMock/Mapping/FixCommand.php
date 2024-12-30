<?php

declare(strict_types=1);

namespace App\Command\WireMock\Mapping;

use App\Command\Command;
use App\WireMock\Mapping\Fixer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

use function sprintf;
use function strlen;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

#[AsCommand(name: 'wiremock:mapping:fix', description: 'Fix mapping files')]
final class FixCommand extends Command
{
    public function __construct(private readonly Fixer $fixer, bool $hidden)
    {
        parent::__construct();

        $this->setHidden($hidden);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();
        $finder->in('tests')->name('*.json');
        foreach ($finder as $file) {
            if (!str_contains($file->getPathname(), '/wiremock/')) {
                continue;
            }

            $oldConfig = json_decode($file->getContents(), true, flags: JSON_THROW_ON_ERROR);
            Assert::isMap($oldConfig);

            $mappingsDir = substr(
                string: $file->getPathname(),
                offset: 0,
                length: strpos($file->getPathname(), '/wiremock/') + strlen('/wiremock'),
            );
            $newConfig = $this->fixer->fix($mappingsDir, $file->getPathname(), $oldConfig);

            if ($oldConfig !== $newConfig) {
                $output->writeln(sprintf('fixed %s', $file->getPathname()));
                $content = json_encode($newConfig, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
                Assert::notFalse($file->openFile('w')->fwrite($content));
            }
        }

        return self::SUCCESS;
    }
}
