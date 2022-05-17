<?php

class Logging
{
    /** @var bool */
    public static $quiet = false;

    /** @var bool */
    public static $verbose = false;

    /**
     * @param string $message
     * @param bool $eol
     * @return void
     */
    public static function error(string $message, bool $eol = true) : void
    {
        self::write($message, $eol);
    }

    /**
     * @param string $message
     * @param bool $eol
     * @return void
     */
    public static function info(string $message, bool $eol = true) : void
    {
        self::write($message, $eol);
    }

    /**
     * @param string $message
     * @param bool $eol
     * @return void
     */
    public static function verbose(string $message, bool $eol = true) : void
    {
        if (!self::$verbose) {
            return;
        }
        self::write($message, $eol);
    }

    /**
     * @param string $message
     * @param bool $eol
     * @return void
     */
    public static function write(string $message, bool $eol) : void
    {
        if (self::$quiet) {
            return;
        }

        $message .= ($eol ? PHP_EOL : '');
        echo $message;
    }

    /**
     * @param array $data
     * @param int $padding
     * @param string $separator
     * @return void
     */
    public static function table(array $data, int $padding = 1, string $separator = '|') : void
    {
        if (count($data) == 0) {
            Logging::info("Dataset is empty");
            return;
        }
        // First calculate the longest values for each column.
        $widths = self::getColumnWidths($data);

        // Create format.
        $format = self::generatePrintFormat($widths, $padding, $separator);

        // Show table.
        $dottedRow = '';
        foreach ($data as $i => $item) {
            $line = sprintf($format, ...$item);
            if ($i == 0) {
                // This is the header, add a line.
                $dottedRow = $separator . str_repeat('-', strlen($line) - 2) . $separator;
                Logging::info($dottedRow);
            }
            Logging::info($line);
            if ($i == 0) {
                Logging::info($dottedRow);
            }
        }
        Logging::info($dottedRow);
    }

    /**
     * @param array $data
     * @return array
     */
    protected static function getColumnWidths(array $data) : array
    {
        $widths = array_fill(0, count($data[0]), 0);

        foreach ($data as $item) {
            foreach ($item as $i => $value) {
                if (strlen($value) > $widths[$i]) {
                    $widths[$i] = strlen($value);
                }
            }
        }
        return $widths;
    }

    /**
     * @param array $widths
     * @param int $padding
     * @param string $separator
     * @return string
     */
    protected static function generatePrintFormat(array $widths, int $padding, string $separator) : string
    {
        $format = [];
        foreach ($widths as $width) {
            $width += $padding;
            $format[] = "%-{$width}s";
        }

        return $separator . ' ' . implode($separator . ' ', $format) . $separator;
    }
}
