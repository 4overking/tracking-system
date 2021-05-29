<?php

declare(strict_types=1);

namespace App\Service\Storage;

use App\Entity\Visit;
use App\Service\VisitTypeRecognizer;
use Doctrine\ORM\EntityManagerInterface;

class VisitStorage implements VisitStorageInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var VisitTypeRecognizer
     */
    private $recognizer;

    public function __construct(EntityManagerInterface $entityManager, VisitTypeRecognizer $recognizer)
    {
        $this->entityManager = $entityManager;
        $this->recognizer = $recognizer;
    }

    public function save(Visit $visit): void
    {
        $this->recognizer->recognize($visit);

        $this->entityManager->persist($visit);
        $this->entityManager->flush();
    }
}
