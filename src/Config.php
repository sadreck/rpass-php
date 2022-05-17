<?php

class Config
{
    /** @var array */
    protected $data = [];

    /** @var string */
    protected $configFile = '';

    /** @var array */
    protected $validOptions = [
        'storage',
        'hostname',
        'port',
        'https',
        'method',
        'verifySSL',
        'sendHostname',
        'gpgPath'
    ];

    /**
     * @return array
     */
    public function all() : array
    {
        return $this->data;
    }

    /**
     * @param string $configFile
     * @throws Exception
     */
    public function __construct(string $configFile)
    {
        $this->configFile = $configFile;
        $this->data = $this->load($this->configFile);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isValidOption(string $name) : bool
    {
        return in_array($name, $this->getValidOptions());
    }

    /**
     * @return array|string[]
     */
    public function getValidOptions() : array
    {
        return $this->validOptions;
    }

    /**
     * @param string $configFile
     * @return array
     * @throws Exception
     */
    protected function load(string $configFile) : array
    {
        if (!file_exists($configFile)) {
            return [];
        } elseif (!is_readable($configFile)) {
            throw new Exception("Config file {$configFile} is not readable.");
        } elseif (!is_writable($configFile)) {
            throw new Exception("Config file {$configFile} is not writable.");
        }

        $data = json_decode(file_get_contents($configFile), true);
        return $data ?? [];
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param $value
     * @return bool
     * @throws Exception
     */
    public function set(string $name, $value) : bool
    {
        if (!$this->isValidOption($name)) {
            throw new Exception("Invalid option: {$name}");
        }

        switch (strtolower($value)) {
            case 'true':
            case 'yes':
                $value = true;
                break;
            case 'false':
            case 'no':
                $value = false;
                break;
            default:
                if (is_numeric($value)) {
                    $value = floor($value) != $value ? (float)$value : (int)$value;
                }
                break;
        }

        $this->data[$name] = $value;
        return (bool) file_put_contents($this->configFile, json_encode($this->data));
    }
}
