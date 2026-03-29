<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (! defined('BASEPATH')) {
    define('BASEPATH', __DIR__);
}

if (! class_exists('CI_Controller')) {
    class CI_Controller
    {
    }
}

require_once __DIR__ . '/../../controllers/Health.php';

class FakeHealthDb
{
    /** @var bool */
    public $db_debug = true;

    /** @var bool */
    private $shouldInitialize;

    public function __construct(bool $shouldInitialize)
    {
        $this->shouldInitialize = $shouldInitialize;
    }

    public function initialize(): bool
    {
        return $this->shouldInitialize;
    }
}

class FakeHealthRedis
{
    /** @var bool */
    private $supported;

    public function __construct(bool $supported)
    {
        $this->supported = $supported;
    }

    public function is_supported(): bool
    {
        return $this->supported;
    }
}

class FakeHealthCache
{
    /** @var FakeHealthRedis */
    public $redis;

    public function __construct(FakeHealthRedis $redis)
    {
        $this->redis = $redis;
    }
}

class FakeHealthOutput
{
    /** @var int */
    public $statusCode = 200;

    /** @var string */
    public $contentType = '';

    /** @var string */
    public $body = '';

    public function set_status_header(int $code)
    {
        $this->statusCode = $code;

        return $this;
    }

    public function set_content_type(string $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function set_output(string $output)
    {
        $this->body = $output;

        return $this;
    }
}

class FakeHealthLoader
{
    /** @var FakeHealthDb */
    private $db;

    /** @var bool */
    private $driverAvailable;

    public function __construct(FakeHealthDb $db, bool $driverAvailable)
    {
        $this->db = $db;
        $this->driverAvailable = $driverAvailable;
    }

    public function database($group = '', $return = true)
    {
        return $this->db;
    }

    public function driver(string $library, array $params = []): bool
    {
        return $this->driverAvailable;
    }
}

class TestableHealthController extends Health
{
    /** @var FakeHealthLoader */
    public $load;

    /** @var FakeHealthDb */
    public $db;

    /** @var FakeHealthCache */
    public $cache;

    /** @var FakeHealthOutput */
    public $output;

    public function __construct(FakeHealthLoader $load, FakeHealthDb $db, FakeHealthCache $cache, FakeHealthOutput $output)
    {
        $this->load = $load;
        $this->db = $db;
        $this->cache = $cache;
        $this->output = $output;
    }
}

final class HealthControllerTest extends TestCase
{
    public function testCheckReturnsUpWhenDatabaseAndCacheAreAvailable(): void
    {
        $db = new FakeHealthDb(true);
        $load = new FakeHealthLoader($db, true);
        $cache = new FakeHealthCache(new FakeHealthRedis(true));
        $output = new FakeHealthOutput();

        $controller = new TestableHealthController($load, $db, $cache, $output);
        $controller->check();

        self::assertSame(200, $output->statusCode);
        self::assertSame('application/json', $output->contentType);

        $payload = json_decode($output->body, true);

        self::assertIsArray($payload);
        self::assertSame('UP', $payload['status']);
        self::assertSame('OK', $payload['checks']['database']);
        self::assertSame('OK', $payload['checks']['cache']);
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $payload['timestamp']);
    }

    public function testCheckReturnsDownWhenDatabaseIsUnavailable(): void
    {
        $db = new FakeHealthDb(false);
        $load = new FakeHealthLoader($db, true);
        $cache = new FakeHealthCache(new FakeHealthRedis(true));
        $output = new FakeHealthOutput();

        $controller = new TestableHealthController($load, $db, $cache, $output);
        $controller->check();

        self::assertSame(503, $output->statusCode);

        $payload = json_decode($output->body, true);

        self::assertIsArray($payload);
        self::assertSame('DOWN', $payload['status']);
        self::assertSame('DOWN', $payload['checks']['database']);
        self::assertSame('OK', $payload['checks']['cache']);
    }
}
