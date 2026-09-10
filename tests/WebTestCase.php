<?php

namespace App\Tests;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Dotenv\Dotenv;

abstract class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass(): string
    {
        self::loadTestEnvironment();

        return Kernel::class;
    }

    private static function loadTestEnvironment(): void
    {
        $_SERVER['APP_ENV'] ??= 'test';
        $_ENV['APP_ENV'] ??= 'test';

        if (isset($_ENV['KERNEL_CLASS']) || isset($_SERVER['KERNEL_CLASS'])) {
            return;
        }

        (new Dotenv())->bootEnv(\dirname(__DIR__).'/.env');
    }
}
