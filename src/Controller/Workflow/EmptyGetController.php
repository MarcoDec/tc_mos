<?php

namespace App\Controller\Workflow;

use Symfony\Component\HttpFoundation\Request;

class EmptyGetController
{
    public function __invoke(Request $request): bool
    {
        return true;
    }
}