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
    const LOG_LEVEL_WARN = 3;
    const LOG_LEVEL_ERROR = 4;

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
        if ($log_level >= $this->log_level) echo $message;
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
