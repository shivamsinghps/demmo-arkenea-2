<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Serializer\Normalizer;

use FOS\JsRoutingBundle\Response\RoutesResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class RoutesResponseNormalizer
 */
class RoutesResponseNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($data, $format = null, array $context = array())
    {
        return array(
            'base_url' => $data->getBaseUrl(),
            'routes'   => $data->getRoutes(),
            'prefix'   => $data->getPrefix(),
            'host'     => $data->getHost(),
            'port'     => $data->getPort(),
            'scheme'   => $data->getScheme(),
            'locale'   => $data->getLocale(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof RoutesResponse;
    }
}
