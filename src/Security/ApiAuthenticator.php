<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;

final class ApiAuthenticator extends EmployeeAuthenticator {
    protected function getLoginUrl(Request $request): string {
        return $this->urlGenerator->generate('login');
    }
}
