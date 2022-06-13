<?php

namespace FMT\InfrastructureBundle;

use FMT\InfrastructureBundle\Helper\CacheHelper;
use FMT\InfrastructureBundle\Helper\LockHelper;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Helper\NotificationHelper;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class InfrastructureBundle extends Bundle
{
    public function boot()
    {
        if ($this->container->has("fmt.logger")) {
            LogHelper::init($this->container->get("fmt.logger"));
        }

        if ($this->container->has("fmt.mailer")) {
            NotificationHelper::init(
                $this->container->get("fmt.mailer"),
                $this->container->getParameter("sender_name"),
                $this->container->getParameter("sender_address")
            );
        }

        if ($this->container->has("fmt.cache")) {
            $prefix = null;
            if ($this->container->hasParameter("cache_prefix")) {
                $prefix = (string) $this->container->getParameter("cache_prefix");
            }
            CacheHelper::init($this->container->get("fmt.cache"), $prefix);
        }

        if ($this->container->has("fmt.lock")) {
            LockHelper::init($this->container->get("fmt.lock"));
        }
    }
}
