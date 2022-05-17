<?php

class HttpClient
{
    /** @var string */
    protected $hostname = 'www.remotepassword.com';

    /** @var bool */
    protected $secure = true;

    /** @var int */
    protected $port = 443;

    /** @var string */
    protected $method = 'post';

    /** @var bool */
    protected $verifyHost = true;

    /** @var array */
    protected $debug = [];

    /** @var string */
    protected $error = '';

    /** @var bool */
    protected $sendHostname = false;

    /**
     * @return array
     */
    public function getDebug(): array
    {
        return $this->debug;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param bool $sendHostname
     * @return HttpClient
     */
    public function setSendHostname(bool $sendHostname): HttpClient
    {
        $this->sendHostname = $sendHostname;
        return $this;
    }

    /**
     * @return string
     */
    protected function getUserAgent() : string
    {
        $data = [
            PHP_OS,
            'RPass/' . __VERSION__,
            'PHP/' . phpversion()
        ];
        if ($this->sendHostname) {
            $data[] = 'Hostname/' . php_uname('n');
        }
        return trim(implode('; ', $data));
    }

    /**
     * @param string $hostname
     * @param int $port
     * @param bool $https
     * @param string $httpMethod
     * @param bool $verifyHost
     * @throws Exception
     */
    public function __construct(string $hostname, int $port, bool $https, string $httpMethod, bool $verifyHost)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->secure = $https;
        $this->method = strtolower($httpMethod) == 'get' ? 'get' : 'post';
        $this->verifyHost = $verifyHost;

        if (!function_exists('curl_init')) {
            throw new Exception('PHP Extension "curl" is not installed.');
        }
    }

    /**
     * @param string $token1
     * @param string $token2
     * @param string|null $format
     * @return string
     */
    public function fetch(string $token1, string $token2, ?string $format = 'raw') : string
    {
        $endpoint = $this->getEndpoint();
        $params = [
            'token1' => $token1,
            'token2' => $token2,
            'format' => empty($format) ? 'raw' : $format
        ];

        return $this->method == 'get'
            ? $this->request($endpoint . '?' . http_build_query($params), [])
            : $this->request($endpoint, $params);
    }

    /**
     * @return string
     */
    protected function getEndpoint() : string
    {
        $url = [];
        $url[] = $this->secure ? 'https://' : 'http://';
        $url[] = $this->hostname;
        $url[] = in_array($this->port, [80, 443]) ? '' : ":{$this->port}";
        $url[] = '/password';
        return implode('', $url);
    }

    /**
     * @param string $endpoint
     * @param array $postParams
     * @return string
     */
    protected function request(string $endpoint, array $postParams) : string
    {
        $options = [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => $this->verifyHost ? 2 : 0,
            CURLOPT_SSL_VERIFYPEER => $this->verifyHost ? 2 : 0,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $this->getUserAgent()
        ];

        if (count($postParams) > 0) {
            $options[CURLOPT_POSTFIELDS] = $postParams;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $output = curl_exec($ch);
        $this->debug = curl_getinfo($ch);
        $this->error = curl_error($ch);
        if ($this->debug['http_code'] != 200) {
            Logging::verbose("HTTP Response: " . $this->debug['http_code']);
            Logging::verbose($this->getError());
        }

        curl_close($ch);
        return $output;
    }
}
