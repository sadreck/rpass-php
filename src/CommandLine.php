<?php

class CommandLine
{
    /** @var string */
    protected $action = null;

    /** @var string */
    protected $name = null;

    /** @var string */
    protected $token1 = null;

    /** @var string */
    protected $token2 = null;

    /** @var string */
    protected $value = null;

    /** @var string */
    protected $checksum = null;

    /** @var string */
    protected $format = null;

    /** @var bool */
    protected $verbose = false;

    /** @var bool */
    protected $list = null;

    /** @var string */
    protected $key = null;

    /** @var string[] */
    protected $validActions = [
        'add',
        'clear',
        'config',
        'delete',
        'get',
        'help',
        'list',
        'version',
        'view'
    ];

    /**
     * @return string|null
     */
    public function getAction() : ?string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return CommandLine
     */
    protected function setAction(string $action): CommandLine
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CommandLine
     */
    protected function setName(string $name): CommandLine
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken1(): ?string
    {
        return $this->token1;
    }

    /**
     * @param string $token1
     * @return CommandLine
     */
    protected function setToken1(string $token1): CommandLine
    {
        $this->token1 = $token1;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken2(): ?string
    {
        return $this->token2;
    }

    /**
     * @param string $token2
     * @return CommandLine
     */
    protected function setToken2(string $token2): CommandLine
    {
        $this->token2 = $token2;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return CommandLine
     */
    protected function setValue(string $value): CommandLine
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    /**
     * @param string|null $checksum
     * @return $this
     */
    public function setChecksum(?string $checksum): CommandLine
    {
        $this->checksum = $checksum;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): ?string
    {
        return empty($this->format) ? 'raw' : $this->format;
    }

    /**
     * @param string|null $format
     * @return $this
     */
    public function setFormat(?string $format): CommandLine
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * @param bool $verbose
     * @return CommandLine
     */
    public function setVerbose(bool $verbose): CommandLine
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * @return bool
     */
    public function isList(): ?bool
    {
        return $this->list;
    }

    /**
     * @param bool $list
     * @return CommandLine
     */
    public function setList(?bool $list): CommandLine
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return CommandLine
     */
    public function setKey(?string $key): CommandLine
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $action
     * @return bool
     */
    protected function isValidAction(string $action) : bool
    {
        return in_array($action, $this->validActions);
    }

    /**
     * @param array $argv
     * @throws Exception
     */
    public function __construct(array $argv)
    {
        $this->parse($argv);
    }

    /**
     * @param array $argv
     * @return void
     * @throws Exception
     */
    protected function parse(array $argv)
    {
        if (count($argv) == 0) {
            return;
        }

        if ($this->isValidAction($argv[0])) {
            // Remove the first element from the array.
            $this->setAction(array_shift($argv));
        } else {
            // Assume it's ./rpass <name>
            $this->setAction('get')->setName(array_shift($argv));
        }

        for ($i = 0; $i < count($argv); $i++) {
            // First check if there is an argument AFTER the argument that begins with "--".
            if (substr($argv[$i], 0, 2) == '--' && !isset($argv[$i + 1])) {
                // These are the switches.
                if (!in_array($argv[$i], ['--verbose', '--list'])) {
                    throw new Exception("Argument {$argv[$i]} has no value set.");
                }
            }

            // And now continue with everything else.
            switch ($argv[$i]) {
                case '--name':
                    $this->setName($argv[++$i]);
                    break;
                case '--token1':
                    $this->setToken1($argv[++$i]);
                    break;
                case '--token2':
                    $this->setToken2($argv[++$i]);
                    break;
                case '--value':
                    $this->setValue($argv[++$i]);
                    break;
                case '--checksum':
                    $this->setChecksum($argv[++$i]);
                    break;
                case '--key':
                    $this->setKey($argv[++$i]);
                    break;
                case '--format':
                    $this->setFormat($argv[++$i]);
                    break;
                case '--verbose':
                    $this->setVerbose(true);
                    break;
                case '--list':
                    $this->setList(true);
                    break;
                default:
                    throw new Exception("Unknown argument: {$argv[$i]}");
            }
        }

        $this->validate();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function validate() : void
    {
        switch ($this->getAction()) {
            case 'add':
                if (empty($this->getName())) {
                    throw new Exception("--name not specified");
                } elseif (empty($this->getToken1())) {
                    throw new Exception("--token1 not specified");
                } elseif (empty($this->getToken2())) {
                    throw new Exception("--token2 not specified");
                } elseif (empty($this->getKey())) {
                    throw new Exception("--key not specified");
                }
                break;
            case 'config':
                if (!$this->isList()) {
                    if (empty($this->getName())) {
                        throw new Exception("--name not specified");
                    }
                }
                break;
            case 'delete':
            case 'view':
                if (empty($this->getName())) {
                    throw new Exception("--name not specified");
                }
                break;
            case 'get':
                /*
                 * Can be:
                 *  --name NAME
                 *  --token1 AAAA --token2 BBB
                 */
                if (empty($this->getName())) {
                    if (empty($this->getToken1()) && empty($this->getToken2())) {
                        throw new Exception("Please specify either the name or the 2 tokens");
                    }

                    if (empty($this->getToken1()) || empty($this->getToken2())) {
                        throw new Exception("Please specify both tokens");
                    }
                }
                break;
            case 'clear':
            case 'list':
            case 'version':
                // Nothing needed here.
                break;
        }
    }
}
