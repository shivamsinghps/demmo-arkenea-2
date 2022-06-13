<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\PublicBundle\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExceptionController
 * @package FMT\PublicBundle\Controller\Common
 * @var Response
 * @package FMT\PublicBundle\Controller
 */
class NotFoundController extends AbstractBaseController
{
    const ROUTE_404 = 'page-not-found';

    /**
     * @Route("/page-not-found", methods={"GET"}, name=NotFoundController::ROUTE_404)
     * @return Response
     */
    public function showAction()
    {
        return new Response($this->renderView('@Public/errors/error404.html.twig', [
            'code' => Response::HTTP_NOT_FOUND,
        ]));
    }
}
