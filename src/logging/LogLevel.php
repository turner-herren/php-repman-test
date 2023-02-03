<?php

namespace logging;

use InvalidArgumentException;

enum LogLevel: int
{
    case DEBUG = 100;
    case INFO = 200;
    case NOTICE = 250;
    case ERROR = 400;
    case CRITICAL = 500;
    case ALERT = 550;
    case EMERGENCY = 600;

    public static function findValue(string $value): ?LogLevel {
        $enums = LogLevel::cases();

        foreach ($enums as $enum) {
            if (strcasecmp($enum->name, $value) === 0) {
                return $enum;
            }
        }

        throw new InvalidArgumentException("not support log level {$value}");
    }
}