<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Visit;
use App\Service\Storage\VisitStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Route("/api")
 */
class VisitController extends AbstractController
{
    /**
     * @Route("/visit", name="app_visit_create", methods={"POST"})
     *
     * @ParamConverter("visit", converter="fos_rest.request_body")
     */
    public function create(
        Visit $visit,
        ConstraintViolationListInterface $validationErrors,
        VisitStorageInterface $visitStorage
    ): ?Response {
        if (\count($validationErrors) > 0) {
            return $this->buildValidationErrorResponse($validationErrors);
        }

        $visitStorage->save($visit);

        return new Response('', Response::HTTP_CREATED);
    }

    private function buildValidationErrorResponse(ConstraintViolationListInterface $violationList): Response
    {
        $data = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($violationList as $key => $violation) {
            if (!isset($data[$violation->getPropertyPath()])) {
                $data[$violation->getPropertyPath()] = [];
            }
            $data[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $this->json($data, Response::HTTP_BAD_REQUEST);
    }
}
