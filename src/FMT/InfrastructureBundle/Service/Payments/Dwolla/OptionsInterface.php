<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla;

/**
 * Interface OptionsInterface
 */
interface OptionsInterface
{
    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @return string
     */
    public function getClientId(): string;

    /**
     * @return string
     */
    public function getClientKey(): string;
}
