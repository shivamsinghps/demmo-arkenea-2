<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla;

use DwollaSwagger\Configuration;
use DwollaSwagger\TokensApi;

/**
 * Class ApiClient
 *
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ApiClient extends \DwollaSwagger\ApiClient
{
    /**
     * @inheritDoc
     */
    public function callApi($resourcePath, $method, $queryParams, $postData, $headerParams, $authSettings)
    {
        if (Configuration::$access_token === '' && ($method !== self::$POST || $resourcePath !== '/token')) {
            $tokensApi = new TokensApi();
            Configuration::$access_token = $tokensApi->token()->access_token;
        }

        return parent::callApi(
            $resourcePath,
            $method,
            $queryParams,
            $postData,
            $headerParams,
            $authSettings
        );
    }
}
