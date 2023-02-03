<?php

namespace logging;

require_once __DIR__ . "/../../src/config/LogConfig.php";

use config\LogConfig;
use Exception;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Throwable;

class LogManager
{

    private static LogManager $instance;

    private Logger $logger;

    /**
     * @throws Exception
     */
    public static function initialize(LogConfig $logConfig, array $loggers = []): LogManager {
        try {
            if (self::$instance !== null) {
                throw new Exception('already initialized');
            }
        } catch (\Throwable $e) {
            if ($e->getMessage() == 'already initialized') {
                throw $e;
            }
        }

        self::$instance = new LogManager($logConfig, $loggers);
        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public static function getInstance(): LogManager
    {
        if(self::$instance == null) {
            throw new Exception("not initialize, call initialize first");
        }

        return self::$instance;
    }


    /**
     * @param LogConfig $logConfig
     * @param array $loggers
     * @throws Exception
     */
    private function __construct(LogConfig $logConfig, array $loggers)
    {
        $this->logger = new Logger($logConfig->getLogChannel(), array_merge($this->getDefaultLoggers($logConfig), $loggers));
    }

    /**
     * @throws Exception
     */
    private function getDefaultLoggers(LogConfig $logConfig): array {
        $rotatingFileLogger = new FileLogger($logConfig->getLogFile(), $logConfig->getLogRetentionDate(), $this->getLogLevel($logConfig->getLogLevel()));
        $defaultLogger = [
            $rotatingFileLogger->getRotationFileHandler()
        ];

        if ($logConfig->getCloudWatchConfig() != null) {
            array_push($defaultLogger, $this->getCloudWatchLogger($logConfig));
        }
        return $defaultLogger;
    }

    /**
     * @throws Exception
     */
    private function getCloudWatchLogger(LogConfig $logConfig): CloudWatch {
        $cloudWatchLogger = new CloudWatchLogger($logConfig->getCloudWatchConfig(), $logConfig->getLogRetentionDate(), $this->getLogLevel($logConfig->getLogLevel()));
        return $cloudWatchLogger->getCloudWatchLogHandler();
    }

    private function getLogLevel(string $logLevel): int {
        return LogLevel::findValue($logLevel)->value;
    }

    public function setLogHead(string $head): void {
        $this->logger = $this->logger->withName($head);
    }

    public function info($msg, ...$context): void
    {
        $this->logger->info($msg, array_merge($context));
    }

    public function error($msg, Throwable $context = null): void
    {
        $exceptionParam = [];
        if ($context != null) {
            $exceptionParam = array_merge($exceptionParam, array($context->getMessage(), $context->getTraceAsString()));
        }
        $this->logger->error($msg, $exceptionParam);
    }

    public function alert($msg, ...$context): void
    {
        $this->logger->alert($msg, array($context));
    }

    public function debug($msg, ...$context): void
    {
        $this->logger->debug($msg, array_merge($context));
    }
}