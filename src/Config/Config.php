<?php
namespace Build\Config;

use Build\Contract\ConfigInterface;

class Config implements ConfigInterface {

    /**
     * @var array
     */
    private array $configs = [];

    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    public function get(string $key, $default = null)
    {
        return $this->configs[$key] ?? $default;
    }

    public function set(string $key, $value)
    {
        $this->configs[$key] = $value;
    }

    public function has(string $keys): bool
    {
        return isset($this->configs[$keys]);
    }
}
