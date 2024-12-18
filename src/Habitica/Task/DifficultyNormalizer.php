<?php

declare(strict_types=1);

namespace App\Habitica\Task;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

use function sprintf;

final class DifficultyNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private const float TRIVIAL = 0.1;
    private const int EASY = 1;
    private const float MEDIUM = 1.5;
    private const int HARD = 2;

    public function getSupportedTypes(?string $format): array
    {
        return [Difficulty::class => true];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Difficulty;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): int|float
    {
        Assert::isInstanceOf($data, Difficulty::class);

        return match ($data) {
            Difficulty::TRIVIAL => self::TRIVIAL,
            Difficulty::EASY => self::EASY,
            Difficulty::MEDIUM => self::MEDIUM,
            Difficulty::HARD => self::HARD,
        };
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return Difficulty::class === $type;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Difficulty
    {
        Assert::same($type, Difficulty::class);
        Assert::numeric($data);

        return match ($data) {
            self::TRIVIAL => Difficulty::TRIVIAL,
            self::EASY => Difficulty::EASY,
            self::MEDIUM => Difficulty::MEDIUM,
            self::HARD => Difficulty::HARD,
            default => throw new InvalidArgumentException(sprintf('Invalid difficulty value: %s', $data)),
        };
    }
}
