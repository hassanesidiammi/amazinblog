<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ViolationTrait
{
    protected function jsonViolation($errors, $code = JsonResponse::HTTP_BAD_REQUEST)
    {
        $formattedErrors = array_map(fn($violation) => [
            'field' => $violation->getPropertyPath(),
            'message' => $violation->getMessage(),
        ], iterator_to_array($errors));

        return $this->json(['errors' => $formattedErrors], $code);
    }
}
