<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Visit;
use App\Repository\VisitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class PurchasesController extends AbstractController
{
    /**
     * @TODO better to use DTO, but my time is limited
     *
     * @Route("/purchases", name="app_purchases_list")
     */
    public function index(VisitRepository $repository): Response
    {
        $allPurchasesWinnerVisits = $repository->getAllPurchasesWinners();

        return $this->json(
            $this->filterWinnersByType($allPurchasesWinnerVisits)
        );
    }

    /**
     * @TODO I'm not sure about performance this method, maybe better to use third nested query instead of this filter
     *
     * @param Visit[] $visits
     */
    private function filterWinnersByType(array $visits): array
    {
        $filtered = [];

        foreach ($visits as $visit) {
            if (Visit::TYPE_OURS === $visit->getType()) {
                $filtered[] = ['client_id' => $visit->getClientId(), 'partner_link' => $visit->getReferer()];
            }
        }

        return $filtered;
    }
}
