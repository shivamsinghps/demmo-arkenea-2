<?php
/**
 * Author: Anton Orlov
 * Date: 20.03.2018
 * Time: 12:01
 */

namespace FMT\InfrastructureBundle\Helper;

use Psr\Log\LoggerInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class LogHelper
{
    /** @var LoggerInterface */
    private static $logger;

    /** @var VarCloner */
    private static $cloner;

    /** @var CliDumper */
    private static $dumper;

    /** @var bool */
    private static $allowTraceBack = true;

    /**
     * @param LoggerInterface $logger
     */
    public static function init(LoggerInterface $logger)
    {
        if (empty(self::$logger)) {
            self::$logger = $logger;
        }

        if (class_exists("Symfony\\Component\\VarDumper\\Cloner\\VarCloner")) {
            self::$cloner = new VarCloner();
        }

        if (class_exists("Symfony\\Component\\VarDumper\\Dumper\\CliDumper")) {
            self::$dumper = new CliDumper();
        }
    }

    /**
     * @param array ...$message
     */
    public static function debug(...$message)
    {
        if (!empty(self::$logger)) {
            self::$logger->debug(call_user_func_array([__CLASS__, "formatMessage"], $message), self::getCaller());
        }
    }

    /**
     * @param array ...$message
     */
    public static function info(...$message)
    {
        if (!empty(self::$logger)) {
            self::$logger->info(call_user_func_array([__CLASS__, "formatMessage"], $message), self::getCaller());
        }
    }

    /**
     * @param array ...$message
     */
    public static function notice(...$message)
    {
        if (!empty(self::$logger)) {
            self::$logger->notice(call_user_func_array([__CLASS__, "formatMessage"], $message), self::getCaller());
        }
    }

    /**
     * @param array ...$message
     */
    public static function warn(...$message)
    {
        if (!empty(self::$logger)) {
            self::$logger->warning(call_user_func_array([__CLASS__, "formatMessage"], $message), self::getCaller());
        }
    }

    /**
     * @param array ...$message
     */
    public static function error(...$message)
    {
        if (!empty(self::$logger)) {
            self::$logger->error(call_user_func_array([__CLASS__, "formatMessage"], $message), self::getCaller());
        }
    }

    /**
     * @param array ...$message
     */
    public static function critical(...$message)
    {
        if (!empty(self::$logger)) {
            self::$logger->critical(call_user_func_array([__CLASS__, "formatMessage"], $message), self::getCaller());
        }
    }

    /**
     * @param array ...$message
     * @return mixed|string
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private static function formatMessage(...$message)
    {
        $result = "";
        $text = array_shift($message);
        if (is_null($text)) {
            $result = "NULL";
        } elseif (is_bool($text)) {
            $result = $text ? "TRUE" : "FALSE";
        } elseif (is_array($text)) {
            $result = print_r($text, true);
        } elseif (is_resource($text)) {
            if (rewind($text)) {
                $result = stream_get_contents($text) . PHP_EOL;
            }
        } elseif (is_string($text)) {
            array_unshift($message, $text);
            $result = call_user_func_array('sprintf', $message);
        } elseif (!empty(self::$cloner) && !empty(self::$dumper)) {
            $result = self::$dumper->dump(self::$cloner->cloneVar($text), true);
        } else {
            $result = (string) $text;
        }

        return $result;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private static function getCaller()
    {
        if (!self::$allowTraceBack) {
            return [];
        }

        $result = ["Called"];
        $trace = debug_backtrace();
        if (count($trace) >= 3) {
            $item = $trace[2];
            $hasClass = isset($item["class"]) && !empty($item["class"]);
            $hasType = isset($item["type"]) && !empty($item["type"]);
            $hasFunction = isset($item["function"]) && !empty($item["function"]);
            if ($hasClass || $hasType || $hasFunction) {
                $result[] = " from ";
            }
            if ($hasClass && $hasType) {
                $result[] = $item["class"];
                $result[] = $item["type"];
            }
            if ($hasFunction) {
                $result[] = $item["function"];
                $result[] = "()";
            }
        }

        if (count($trace) >= 2) {
            $item = $trace[1];
            if (isset($item["file"]) && !empty($item["file"])) {
                $result[] = " on ";
                $result[] = $item["file"];
                $result[] = ":";
                if (isset($item["line"]) && !empty($item["line"])) {
                    $result[] = $item["line"];
                }
            }
        }

        return count($result) > 1 ? [join("", $result)] : [];
    }
}
