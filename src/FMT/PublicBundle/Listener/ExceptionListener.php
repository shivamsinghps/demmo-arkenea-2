<?php
/**
 * Author: Anton Orlov
 * Date: 20.03.2018
 * Time: 19:10
 */

namespace FMT\PublicBundle\Listener;

use FMT\DataBundle\Entity\User;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\PublicBundle\Controller\Common\NotFoundController;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class ExceptionListener
 * @package FMT\PublicBundle\Listener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class ExceptionListener
{
    /** @var EngineInterface */
    private $engine;

    /** @var bool */
    private $debug;

    /** @var RouterInterface */
    private $router;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        EngineInterface $engine,
        $debug,
        RouterInterface $router,
        TokenStorageInterface $tokenStorage
    ) {
        $this->engine = $engine;
        $this->debug = (bool) $debug;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onResponseFormatException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();
        $details = [
            "success" => false,
            "code" => 500,
            "host" => $request->getHost(),
            "message" => $exception->getMessage()
        ];

        if ($this->debug) {
            $details["exception"] = get_class($exception);
            $details["backtrace"] = $exception->getTraceAsString();
        }

        if ($exception instanceof HttpException) {
            $details["code"] = $exception->getStatusCode();
        } elseif ($exception instanceof AccessDeniedException) {
            $details["code"] = $this->tokenStorage->getToken()->getUser() instanceof User ?
                Response::HTTP_FORBIDDEN :
                Response::HTTP_UNAUTHORIZED;
        } elseif ($exception instanceof ApiErrorException) {
            $details["code"] = $exception->getHttpStatus();
        }

        LogHelper::error(
            "[%s] %s |\nTrace: %s",
            get_class($exception),
            $exception->getMessage(),
            $exception->getTraceAsString()
        );
        LogHelper::debug($exception->getTraceAsString());

        $event->stopPropagation();

        if ($this->isJsonResponse($request)) {
            $event->setResponse(new JsonResponse($details));
            return;
        }

        switch ($details['code']) {
            case Response::HTTP_NOT_FOUND:
                $route = $this->getRedirectToRoute(NotFoundController::ROUTE_404);
                $response = new RedirectResponse($route);
                break;
            case Response::HTTP_UNAUTHORIZED:
            case Response::HTTP_FORBIDDEN:
            case Response::HTTP_INTERNAL_SERVER_ERROR:
                $template = $this->getTemplate($details);
                $response = new Response($template);
                break;
            default:
                $template = $this->engine->render("TwigBundle:Exception:error.html.twig", $details);
                $response = new Response($template);
        }

        $event->setResponse($response);
    }

    /**
     * @param $routeName
     * @return string
     */
    private function getRedirectToRoute($routeName)
    {
        return $this->router->generate($routeName);
    }

    /**
     * @param array $details
     * @return string
     */
    private function getTemplate(array $details)
    {
        return $this->engine->render(
            sprintf('@Public/errors/error%d.html.twig', $details['code']),
            $details
        );
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isJsonResponse(Request $request)
    {
        $jsonHeades = array_filter([
            $request->headers->get("X-Requested-With") === "XMLHttpRequest",
            $request->getRequestFormat() === "json"
        ]);

        return count($jsonHeades) > 1;
    }
}
