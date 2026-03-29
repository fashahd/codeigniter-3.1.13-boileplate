<?php

declare(strict_types=1);

if (! defined('CI_VERSION')) {
    define('CI_VERSION', '3.1.13');
}

if (! defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

class CI_Controller
{
    /** @var CI_Loader */
    public $load;

    /** @var CI_DB_query_builder */
    public $db;

    /** @var CI_Cache */
    public $cache;

    /** @var CI_Output */
    public $output;

    public function __construct()
    {
    }
}

class CI_Loader
{
    /** @return CI_DB_query_builder */
    public function database($params = '', bool $return = false)
    {
        return new CI_DB_query_builder();
    }

    /** @return bool */
    public function driver(string $library, array $params = [])
    {
        return true;
    }

    public function view(string $view, array $vars = [], bool $return = false)
    {
    }
}

class CI_DB_query_builder
{
    /** @var bool */
    public $db_debug = false;

    /** @return bool */
    public function initialize()
    {
        return true;
    }
}

class CI_Cache
{
    /** @var CI_Cache_redis */
    public $redis;
}

class CI_Cache_redis
{
    /** @return bool */
    public function is_supported()
    {
        return true;
    }
}

class CI_Output
{
    /** @return $this */
    public function set_content_type(string $mime_type, string $charset = '')
    {
        return $this;
    }

    /** @return $this */
    public function set_output(string $output)
    {
        return $this;
    }

    /** @return $this */
    public function set_status_header(int $code = 200, string $text = '')
    {
        return $this;
    }
}
