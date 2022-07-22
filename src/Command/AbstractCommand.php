<?php

namespace App\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;

abstract class AbstractCommand extends Command {
    public function getApplication(): Application {
        $application = parent::getApplication();
        if (empty($application)) {
            throw new RuntimeException('Application not found.');
        }
        return $application;
    }
}
