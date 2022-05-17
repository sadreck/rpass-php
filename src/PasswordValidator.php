<?php

class PasswordValidator
{
    /**
     * @param stdClass $password
     * @param string $data
     * @param $format
     * @return string|null
     */
    public function validate(stdClass $password, string $data, $format) : ?string
    {
        if (empty($password->checksum)) {
            // If we don't have a local checksum value, return a valid response.
            return $data;
        }

        switch ($format) {
            case 'raw':
                if (hash('sha256', $data) == $password->checksum) {
                    return $data;
                }
                break;
            case 'base64':
                if (hash('sha256', base64_decode($data)) == $password->checksum) {
                    return base64_decode($data);
                }
                break;
            case 'json':
                $jsonData = json_decode($data);
                if (is_object($jsonData) && property_exists($jsonData, 'password')) {
                    if (hash('sha256', $jsonData->password) == $password->checksum) {
                        return $jsonData->password;
                    }
                }
                break;
            case 'xml':
                libxml_use_internal_errors(true);
                libxml_disable_entity_loader(true);
                $xml = simplexml_load_string($data);
                if ($xml) {
                    if (isset($xml->password)) {
                        // Line breaks can be a bit weird, so check with both versions.
                        if (hash('sha256', trim($xml->password)) == $password->checksum) {
                            return trim($xml->password);
                        }

                        if (strpos($xml->password, "\r\n") !== false) {
                            $xml->password = str_replace("\r\n", "\n", $xml->password);
                        } else {
                            $xml->password = str_replace("\n", "\r\n", $xml->password);
                        }
                        if (hash('sha256', trim($xml->password)) == $password->checksum) {
                            return trim($xml->password);
                        }
                    }
                }
                break;
        }

        // Invalid.
        return null;
    }
}
