<?php

namespace App\Entity\Interfaces;

use App\Entity\Management\Society\Company\Company;

interface CompanyInterface {
    public function setCompany(Company $company): self;
}
