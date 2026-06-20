<?php

namespace App\Shared\Helpers;

use Illuminate\Log\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\JsonFormatter;

class LoggingFormatter
{
    public function __invoke(Logger $logger): void
    {
        $monolog = $logger->getLogger();
        foreach ($monolog->getHandlers() as $handler) {
            if ($handler instanceof RotatingFileHandler) {
                $handler->setFormatter(new JsonFormatter());
            }
        }
    }
}
