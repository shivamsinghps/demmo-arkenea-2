<?php

namespace FMT\PublicBundle\Controller;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\Mapper\ObjectToArrayMapper;
use FMT\InfrastructureBundle\Helper\FormHelper;
use FMT\PublicBundle\Controller\Common\PublicDashboardController;
use FMT\PublicBundle\Controller\Donor\DashboardController as DonorDashboardController;
use FMT\PublicBundle\Controller\Donor\ProfileController as DonorProfileController;
use FMT\PublicBundle\Controller\Student\DashboardController as StudentDashboardController;
use FMT\PublicBundle\Controller\Student\ProfileController as StudentProfileController;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractBaseController
 * @package FMT\PublicBundle\Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractBaseController
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PaginatorInterface $paginator
     */
    protected $paginator;

    /**
     * @required
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @return AbstractBaseController
     * @required
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;

        return $this;
    }

    /**
     * @param SessionInterface $session
     * @return AbstractBaseController
     * @required
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @param FormFactoryInterface $formFactory
     * @return AbstractBaseController
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;

        return $this;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     * @return AbstractBaseController
     * @required
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    /**
     * @param RouterInterface $router
     * @return AbstractBaseController
     * @required
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @param EngineInterface $templating
     * @return AbstractBaseController
     * @required
     */
    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;

        return $this;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @return $this
     * @required
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @required
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @required
     * @param PaginatorInterface $paginator
     */
    public function setPaginator(PaginatorInterface  $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type The built type of the form
     * @param mixed $data The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormInterface|Form
     */
    protected function createForm($type, $data = null, array $options = [])
    {
        return $this->formFactory->create($type, $data, $options);
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return UserInterface|null|User|NormalizableInterface
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
     * @param int $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl($route, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url The URL to redirect to
     * @param int $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    protected function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route The name of the route
     * @param array $parameters An array of parameters
     * @param int $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute($route, array $parameters = [], $status = 302)
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @param string $message A message
     * @param \Exception|null $previous The previous exception
     *
     * @return NotFoundHttpException
     */
    protected function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    protected function renderView($view, array $parameters = [])
    {
        return $this->templating->render($view, $parameters);
    }

    /**
     * @param mixed $attributes The attributes
     * @param mixed $object The object
     *
     * @return bool
     *
     * @throws \LogicException
     */
    protected function isGranted($attributes, $object = null)
    {
        return $this->authorizationChecker->isGranted($attributes, $object);
    }

    /**
     * @param mixed $attributes The attributes
     * @param mixed $object The object
     * @param string $message The message passed to the exception
     *
     * @throws AccessDeniedException
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, $message = 'Access Denied.')
    {
        if (!$this->isGranted($attributes, $object)) {
            throw new AccessDeniedException($message);
        }
    }

    /**
     * @param $message
     * @param bool $translate
     * @param array $parameters
     */
    protected function addFlashBagNotice($message, $translate = true, $parameters = [])
    {
        if ($translate) {
            $message = $this->translate($message, $parameters);
        }
        $this->session->getFlashBag()->add("success", $message);
    }

    /**
     * @param $message
     * @param bool $translate
     * @param array $parameters
     */
    protected function addFlashBagError($message, $translate = true, $parameters = [])
    {
        if ($translate) {
            $message = $this->translate($message, $parameters);
        }
        $this->session->getFlashBag()->add("error", $message);
    }

    /**
     * @param string|null $type
     */
    protected function clearFlashBag($type)
    {
        if ($type) {
            $this->session->getFlashBag()->get($type);
        } else {
            $this->session->getFlashBag()->all();
        }
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected function getSessionVariable($name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setSessionVariable($name, $value)
    {
        if (is_null($value)) {
            $this->session->remove($name);
        } else {
            $this->session->set($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function hasSessionVariable($name)
    {
        return $this->session->has($name);
    }

    /**
     * @param Request $request
     */
    protected function checkAjaxRequest(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('fmt.ajax.ajax_only');
        }
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    protected function createFailureAjaxResponse($data = null)
    {
        return $this->createAjaxResponse($data, false);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    protected function createSuccessAjaxResponse($data = null)
    {
        return $this->createAjaxResponse($data, true);
    }

    /**
     * @param $data
     * @param $status
     * @param $urlRedirect
     * @return JsonResponse
     */
    protected function createAjaxResponse($data, $status, $urlRedirect = false)
    {
        $messages = $this->session->getFlashBag()->all();

        $response = [
            'success' => $status,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        if (!empty($messages)) {
            $response['messages'] = $messages;
        }

        if ($urlRedirect !== false) {
            $response['redirect'] = $urlRedirect;
        }

        return new JsonResponse($response);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     * @return JsonResponse
     */
    protected function createValidationErrorsResponse(FormInterface $form, $data = [])
    {
        $errors = [
            'errors' => FormHelper::collectErrors($form)
        ];

        return $this->createAjaxResponse(array_merge($data, $errors), false);
    }

    /**
     * @param $source
     * @param int $maxLevel
     * @param int $level
     * @return array
     */
    protected function normalizeObjectsForAjax($source, $maxLevel = 1, $level = 1)
    {
        $result = [];

        if (!is_array($source) && $source instanceof \Traversable) {
            return $result;
        }

        foreach ($source as $item) {
            $result[] = $this->normalizeObjectForAjax($item, $maxLevel, $level);
        }

        return $result;
    }

    /**
     * @param $item
     * @param int $maxLevel
     * @param int $level
     * @return array
     */
    protected function normalizeObjectForAjax($item, $maxLevel = 1, $level = 1)
    {
        if (!is_object($item)) {
            return [];
        }

        return ObjectToArrayMapper::map($item, $maxLevel, $level);
    }

    /**
     * @param string $message
     * @param array $parameters
     * @param string|null $domain
     * @return string
     */
    protected function translate($message, $parameters = [], $domain = null)
    {
        return $this->translator->trans($message, $parameters, $domain);
    }

    /**
     * @param $target
     * @param int $page
     * @param int $limit
     * @param array $options
     * @return PaginationInterface
     */
    protected function paginate($target, int $page = 1, int $limit = 10, $options = [])
    {
        return $this->paginator->paginate($target, $page, $limit, $options);
    }

    /**
     * @param User $user
     * @return string
     */
    protected function generateSuccessRedirectHref(User $user): string
    {
        $roleRouteMap = [
            User::ROLE_DONOR              => DonorDashboardController::ROUTE_INDEX,
            User::ROLE_STUDENT            => StudentDashboardController::ROUTE_INDEX,
            User::ROLE_INCOMPLETE_DONOR   => DonorProfileController::ROUTE_INDEX,
            User::ROLE_INCOMPLETE_STUDENT => StudentProfileController::ROUTE_INDEX,
        ];

        $roles = $user->getRoles();

        foreach ($roleRouteMap as $role => $routeName) {
            if (in_array($role, $roles)) {
                $resultRouteName = $routeName;
                break;
            }
        }

        if (empty($resultRouteName)) {
            $resultRouteName = PublicDashboardController::ROUTE_INDEX;
        }

        return $this->generateUrl($resultRouteName, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
