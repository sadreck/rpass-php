<?php

class PasswordStorage
{
    /** @var string */
    protected $passwordFile = '';

    /** @var array */
    protected $data = [];

    /**
     * @param string $passwordFile
     * @throws Exception
     */
    public function __construct(string $passwordFile)
    {
        $this->passwordFile = $passwordFile;
        $this->data = $this->load($this->passwordFile);
    }

    /**
     * @param string $passwordFile
     * @return array
     * @throws Exception
     */
    protected function load(string $passwordFile) : array
    {
        if (!file_exists($passwordFile)) {
            return [];
        } elseif (!is_readable($passwordFile)) {
            throw new Exception("Password storage file {$passwordFile} is not readable.");
        } elseif (!is_writable($passwordFile)) {
            throw new Exception("Password storage file {$passwordFile} is not writable.");
        }

        $data = json_decode(file_get_contents($passwordFile), true);
        return $data ?? [];
    }

    /**
     * @param string $name
     * @param string $token1
     * @param string $token2
     * @param string|null $checksum
     * @return bool
     */
    public function add(string $name, string $token1, string $token2, ?string $checksum, string $key) : bool
    {
        $this->data[$name] = (object)[
            'name' => $name,
            'token1' => $token1,
            'token2' => $token2,
            'checksum' => $checksum,
            'key' => $key
        ];
        return $this->save();
    }

    /**
     * @return array
     */
    public function all() : array
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return stdClass|null
     */
    public function getByName(string $name) : ?stdClass
    {
        return isset($this->data[$name]) ? (object)$this->data[$name] : null;
    }

    /**
     * @param string $token1
     * @param string $token2
     * @return stdClass|null
     */
    public function getByTokens(string $token1, string $token2) : ?stdClass
    {
        foreach ($this->data as $name => $password) {
            if ($password['token1'] == $token1 && $password['token2'] == $token2) {
                return (object)$password;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function delete(string $name) : bool
    {
        unset($this->data[$name]);
        return $this->save();
    }

    /**
     * @return bool
     */
    public function clear() : bool
    {
        $this->data = [];
        return $this->save();
    }

    /**
     * @return bool
     */
    protected function save() : bool
    {
        return (bool) file_put_contents($this->passwordFile, json_encode($this->data));
    }
}
