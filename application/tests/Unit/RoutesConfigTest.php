<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RoutesConfigTest extends TestCase
{
    public function testHealthRouteIsConfigured(): void
    {
        if (! defined('BASEPATH')) {
            define('BASEPATH', __DIR__);
        }

        /** @var array<string, mixed> $route */
        $route = [];

        require __DIR__ . '/../../config/routes.php';

        self::assertArrayHasKey('default_controller', $route);
        self::assertArrayHasKey('health', $route);
        self::assertSame('welcome', $route['default_controller']);
        self::assertSame('health/check', $route['health']);
    }
}
