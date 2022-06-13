<?php
/**
 * Author: Anton Orlov
 * Date: 21.03.2018
 * Time: 16:15
 */

namespace FMT\InfrastructureBundle\Service\Monolog;

use Monolog\Handler\StreamHandler;

class CliStreamHandler extends StreamHandler
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct($stream, $level, $bubble = true, $filePermission = null, $useLocking = false)
    {
        if (php_sapi_name() != "cli") {
            $level = PHP_INT_MAX;
        }

        parent::__construct($stream, $level, $bubble, $filePermission, $useLocking);
    }
}
