# Description

This is the PHP version of the script that you can use with RemotePassword. For the Python3 version [see here](https://github.com/sadreck/rpass-python).

# Prerequisites

You will need to install the following packages:

```
sudo apt install php-cli php-curl
```

And ensure you're running PHP 7.1+:

```
php --version
```

Ideally you should be using a PHP version that is supported, however I'm aware that some distros still come with older versions.

For supported versions, see: https://www.php.net/supported-versions.php

# Installation

1. Clone or download this repo to your local machine.
2. Make sure `rpass` is executable by running: `chmod +x rpass`.
3. Add the location to your `PATH` to make it easier to use. In Ubuntu for example, this would be by editing your `~/.bashrc` and adding the following line at the bottom: `export PATH=$PATH:/your/absolute/path/to/rpass-php`

To make sure it runs, just execute: `rpass version`

# Configuration

Below are the configuration settings that are supported. To set any of these, use the following syntax:

```
rpass config --name [NAME] --value [VALUE]
```

To retrieve the current value of a setting, just ommit `--value`, such as:

```
rpass config --name [NAME]
```

## Options

| Name    | Description                                                                                                                          | Type | Default                                         |
|---------|--------------------------------------------------------------------------------------------------------------------------------------| ---- |-------------------------------------------------|
| storage | Absolute path to the file that will hold the passwords. Ideally it will have an `*.storage` extension to make it easier to identify. | string | `rpass.storage` in the same path as the script. |
| hostname | Hostname of the server where RPass is hosted. Default is `www.remotepassword.com`.                                                   | string | `www.remotepassword.com`                        |
| port | If the server is running on a port other than 80/443, specify it here.                                                               | integer | `443`                                           |
| https | Whether or not is will be via SSL.                                                                                                   | boolean | `true`                                          |
| method | The HTTP method that will be used to fetch the password.                                                                             | `get` or `post` | `post`                                          |
| verifySSL | Whether to verify is the endpoint SSL certificate is valid.                                                                          | boolean | `true`                                          |
| sendHostname | Whether to include the hostname of the machine in the user agent.                                                                    | boolean | `false`                                         |
| gpgPath | Absolute path to GPG executable. Make sure this is installed.                                                                        | string | `/usr/bin/gpg` |

# Basic Usage

## Adding a password

You can always use the command line that is generated on RemotePassword, but if you decided to do this manually it would look something like this:

```
rpass add --name "Local MySQL" --token1 "Random Token 1" --token2 "Random Token 2" --key "Public Key ID" --checksum "SHA256HASH"
```

The `--checksum` is the SHA256 value of the data you are meant to receive. If it is set it will be checked against the actual returned value, to make sure the data has not been tampered with remotely. Although the use of `--checksum` is optional, it's strongly recommended that you use it.

## Retrieving a password

You can retrieve a password either by the name you used:

```
rpass "Local MySQL"
```

Or by using the tokens directly:

```
rpass get --token1 "Random Token 1" --token2 "Random Token 2"
```

## More Commands

You can either run `rpass help` or view the `help.txt` file that is in this repo for all the supported commands. In addition, you can always add `--versbose` to any command for additional output.

# Support

If something isn't working, please create an issue in this repo with as much detail as possible.