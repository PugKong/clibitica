<?php

declare(strict_types=1);

namespace App\Command\WireMock\Mapping;

use App\WireMock\Mapping\Fixer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

use function sprintf;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

#[AsCommand(name: 'wiremock:mapping:fix', description: 'Fix mapping files')]
final class FixCommand extends Command
{
    public function __construct(private readonly Fixer $fixer)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $mappingsDir = 'wiremock/mappings';

        $finder = new Finder();
        $finder->in($mappingsDir)->name('*.json');
        foreach ($finder as $file) {
            $oldConfig = json_decode($file->getContents(), true, flags: JSON_THROW_ON_ERROR);
            Assert::isMap($oldConfig);

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
