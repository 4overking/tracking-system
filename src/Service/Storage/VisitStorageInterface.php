<?php

declare(strict_types=1);

namespace App\Service\Storage;

use App\Entity\Visit;

interface VisitStorageInterface
{
    public function save(Visit $visit);
}
