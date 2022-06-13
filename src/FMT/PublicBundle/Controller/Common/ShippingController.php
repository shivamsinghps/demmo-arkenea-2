<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DomainBundle\Service\ShippingManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class ShippingController
 *
 * @var Response
 * @var Route
 * @var Template
 * @var Security
 * @var Method
 * @var ParamConverter
 *
 * @package FMT\ShippingController\Controller
 * @Route("/book-shipping")
 * @Template()
 */
class ShippingController extends AbstractBaseController
{
    const ROUTE_OPTIONS = "fmt-common-book-shipping-options";

    /** @var ShippingManagerInterface */
    protected $manager;

    /**
     * @required
     * @param ShippingManagerInterface $manager
     * @return $this
     */
    public function setManager(ShippingManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/options", name=ShippingController::ROUTE_OPTIONS)
     */
    public function optionsAction(Request $request)
    {
        $this->checkAjaxRequest($request);

        $list = $this->manager->getOptions();
        $list = $this->normalizeObjectsForAjax($list, 1);

        return $this->createSuccessAjaxResponse([
            'title' => $this->translate('fmt.campaign.textbooks.popup.shipping.title'),
            'message' => $this->renderView(
                '@Public/components/campaign/shippingOptionsPopup.html.twig',
                ['list' => $list,]
            ),
        ]);
    }
}
