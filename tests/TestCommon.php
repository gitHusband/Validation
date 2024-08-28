<?php

namespace githusband\Tests;

/**
 * Unit tests common class
 * 
 * @package UnitTests
 */
class TestCommon
{
    const LOG_LEVEL_DEBUG = 1;
    const LOG_LEVEL_INFO = 2;
    const LOG_LEVEL_NOTICE = 3;
    const LOG_LEVEL_WARN = 4;
    const LOG_LEVEL_ERROR = 5;

    protected static $log_level_colors = [
        self::LOG_LEVEL_DEBUG   => '',
        self::LOG_LEVEL_INFO    => 'green',
        self::LOG_LEVEL_NOTICE  => 'light_blue',
        self::LOG_LEVEL_WARN    => 'yellow',
        self::LOG_LEVEL_ERROR   => 'red',
    ];

    protected static $foreground_colors = [
        'black'         => '0;30',
        'dark_gray'     => '1;30',
        'red'           => '0;31',
        'light_red'     => '1;31',
        'green'         => '0;32',
        'light_green'   => '1;32',
        'yellow'        => '0;33',
        'light_yellow'  => '1;33',
        'blue'          => '0;34',
        'light_blue'    => '1;34',
        'purple'        => '0;35',
        'light_purple'  => '1;35',
        'cyan'          => '0;36',
        'light_cyan'    => '1;36',
        'light_gray'    => '0;37',
        'white'         => '1;37',
    ];

    protected $log_level = 2;

    protected $opts;

    public function __construct()
    {
        $log_level = getenv('VALIDATION_LOG_LEVEL');
        if (!empty($log_level) && is_numeric($log_level)) $this->set_log_level($log_level);
    }

    protected function set_log_level($log_level)
    {
        $this->log_level = $log_level;
    }

    protected function write_log($log_level, $message)
    {
        if ($log_level >= $this->log_level) echo $this->set_text_color($log_level, $message);
    }

    protected function set_text_color($log_level, $message)
    {
        if (empty(static::$log_level_colors[$log_level])) return $message;

        $foreground = static::$log_level_colors[$log_level];

        $string = "\033[" . static::$foreground_colors[$foreground] . 'm';

        return $string . $message . "\033[0m";
    }

    /**
     * Locate backtrace
     *
     * @param ?array $debug
     * @return array
     */
    public function locate_backtrace($debug = null)
    {
        if ($debug === null) {
            $ingore_index = 0;
            $debug = debug_backtrace();
        } else {
            // Don't ingore
            $ingore_index = -1;
        }

        $backtrace = [];

        $debug_len = count($debug);
        foreach ($debug as $index => $value) {
            if ($index == $ingore_index) continue;
            if (($index + 1) == $debug_len) break;

            $file = isset($value['file']) ? $value['file'] : 'Unknown file';
            $line = isset($value['line']) ? $value['line'] : 'Unknown line';
            $backtrace[] = 'File:' . $file . ' Function:' . $value['function'] . ' Line:' . $line;
        }

        return $backtrace;
    }

    /**
     * Log the exception details
     *
     * @param \Exception|\Throwable $e
     * @return void
     */
    protected function log_exception($e)
    {
        $exception_info = "Exception - " . $e->getFile() . ":" . $e->getLine() . " - " . $e->getCode() . " - " . $e->getMessage();
        $backtrace = $this->locate_backtrace($e->getTrace());
        $backtrace = json_encode($backtrace, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $message = "{$exception_info}\n{$backtrace}\n";
        $this->write_log(static::LOG_LEVEL_ERROR, $message);
    }

    public function help()
    {
        $help_data = [];
        $help_data['test_methods'] = [];

        $class_methods = get_class_methods($this);

        foreach ($class_methods as $method_name) {
            if (preg_match('/^test_.*/', $method_name)) {
                $help_data['test_methods'][] = $method_name;
            }
        }

        $this->write_log(static::LOG_LEVEL_INFO, "Available test methods: \n");
        if (empty($help_data['test_methods'])) $this->write_log(static::LOG_LEVEL_INFO, "  - NOT FOUND\n");
        foreach ($help_data['test_methods'] as $key => $value) {
            $this->write_log(static::LOG_LEVEL_INFO, "  - {$value}\n");
        }
    }

    /**
     * Convert seconds to Human Time - X Days, Y Days, Z Hours, A Minutes, B Seconds
     *
     * @param int $seconds
     * @return string
     */
    protected function seconds_to_human_time($seconds)
    {
        if (empty($seconds)) {
            return '0 Second';
        }

        if ($seconds < 1) {
            return "{$seconds} Second";
        }

        $microtime = fmod($seconds, 1);
        $seconds = floor($seconds);

        $secondsOfOneMinute = 60;
        $secondsOfOneHour = $secondsOfOneMinute * 60;
        $secondsOfOneDay = $secondsOfOneHour * 24;
        $secondsOfOneYear = $secondsOfOneDay * 365;

        $years = floor($seconds / $secondsOfOneYear);
        $seconds = $seconds % $secondsOfOneYear;
        $days = floor($seconds / $secondsOfOneDay);
        $seconds = $seconds % $secondsOfOneDay;
        $hours = floor($seconds / $secondsOfOneHour);
        $seconds = $seconds % $secondsOfOneHour;
        $minutes = floor($seconds / $secondsOfOneMinute);
        $seconds = $seconds % $secondsOfOneMinute;

        $humanTime = '';
        if ($years > 0) {
            $humanTime .= "{$years} Years";
        }
        if ($days > 0) {
            if (!empty($humanTime)) $humanTime .= ", ";
            $humanTime .= "{$days} Day";
            if ($days > 1) $humanTime .= "s";
        }
        if ($hours > 0) {
            if (!empty($humanTime)) $humanTime .= ", ";
            $humanTime .= "{$hours} Hour";
            if ($hours > 1) $humanTime .= "s";
        }
        if ($minutes > 0) {
            if (!empty($humanTime)) $humanTime .= ", ";
            $humanTime .= "{$minutes} Minute";
            if ($minutes > 1) $humanTime .= "s";
        }
        if ($seconds > 0 || $microtime > 0) {
            if (!empty($humanTime)) $humanTime .= ", ";
            $seconds += $microtime;
            $humanTime .= "{$seconds} Second";
            if ($seconds > 1) $humanTime .= "s";
        }

        return $humanTime;
    }

    public function __call($name, $arguments)
    {
        $this->write_log(static::LOG_LEVEL_ERROR, "Method not existed: {$name}.\nPease call \"help\" method for all the available methods.\n");
        // $this->help();
    }
}
