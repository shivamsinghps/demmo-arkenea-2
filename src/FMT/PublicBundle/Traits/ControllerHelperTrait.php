<?php

namespace FMT\PublicBundle\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Trait ControllerHelperTrait
 * @package FMT\PublicBundle\Traits
 */
trait ControllerHelperTrait
{
    /**
     * @param $data
     * @param $templatePath
     * @param bool $success
     * @param bool $redirect
     * @return JsonResponse
     */
    public function prepareJsonResponse($data, $templatePath, $success = false, $redirect = false)
    {
        return new JsonResponse([
            'success' => $success,
            'form' => $this->renderView($templatePath, $data),
            'redirect' => $redirect,
        ], 200);
    }
}
