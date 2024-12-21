<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\ConfigureException;

use App\Command\InputMapper\Exception\ConfigurationException;

interface Example
{
    public static function exception(): ConfigurationException;
}
