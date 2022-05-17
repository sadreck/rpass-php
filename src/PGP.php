<?php

class PGP
{
    /** @var string */
    protected $gpgPath = '';

    /**
     * @param string $gpgPath
     * @throws Exception
     */
    public function __construct(string $gpgPath)
    {
        if (!file_exists($gpgPath)) {
            throw new Exception("GPG Path does not exist: {$gpgPath}");
        } elseif (!is_executable($gpgPath)) {
            throw new Exception("GPG file is not executable: {$gpgPath}");
        }

        $this->gpgPath = $gpgPath;
    }

    /**
     * @param string $data
     * @param string $key
     * @return string|null
     */
    public function decrypt(string $data, string $key) : ?string
    {
        $command = [
            'echo ' . escapeshellarg($data),
            '|',
            $this->gpgPath,
            '--decrypt',
            '--quiet',
            '-r ' . escapeshellarg($key),
            '2>/dev/null'
        ];
        $output = shell_exec(implode(' ', $command));
        return ($output === false || $output === null) ? null : $output;
    }
}
