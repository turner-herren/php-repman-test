<?php

namespace logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class FileLogger
{
    private string $logFile;
    private int $logRetentionDate;
    private int $logLevel;

    /**
     * @param string $logFile
     * @param int $logRetentionDate
     * @param int $logLevel
     */
    public function __construct(string $logFile, int $logRetentionDate, int $logLevel)
    {
        $this->logFile = $logFile;
        $this->logRetentionDate = $logRetentionDate;
        $this->logLevel = $logLevel;
    }


    public function getRotationFileHandler(): RotatingFileHandler
    {
        $defaultHandler = new RotatingFileHandler($this->logFile, $this->logRetentionDate, $this->logLevel, true, 0644);

        $outputFormat = "[%datetime%]-[%level_name%]-[%channel%]: %message% { Parameters : %context% }\n";

        $defaultHandler -> setFormatter(new LineFormatter($outputFormat));
        return $defaultHandler;
    }
}