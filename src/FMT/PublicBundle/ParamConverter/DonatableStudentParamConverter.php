<?php
/**
 * Author: Anton Orlov
 * Date: 20.04.2018
 * Time: 19:23
 */

namespace FMT\PublicBundle\ParamConverter;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\ParamConverter\Traits\InheritanceTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DonatableStudentParamConverter
 * @package FMT\PublicBundle\ParamConverter
 */
class DonatableStudentParamConverter implements ParamConverterInterface
{
    use InheritanceTrait;

    /** @var UserManagerInterface */
    private $manager;

    public function __construct(UserManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request $request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $result = null;
        $options = $configuration->getOptions();
        $key = array_key_exists("id", $options) ? $options["id"] : "id";

        if ($request->attributes->has($key)) {
            $result = $this->manager->getActiveStudent($request->attributes->getInt($key));
            if (is_null($result) || !$result->hasUnfinishedCampaign()) {
                throw new NotFoundHttpException();
            }
        }

        $request->attributes->set($configuration->getName(), $result);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();
        $hasActiveCampaign = isset($options["active_campaign"]) && $options["active_campaign"];
        return $hasActiveCampaign && $this->isInstanceOf($configuration->getClass(), User::class);
    }
}
