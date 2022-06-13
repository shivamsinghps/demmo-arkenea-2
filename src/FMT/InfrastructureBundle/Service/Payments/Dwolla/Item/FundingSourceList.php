<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class FundingSourceList
 */
class FundingSourceList
{
    /**
     * @var FundingSource[]
     */
    protected $fundingSources;

    /**
     * @var ListInformation|null
     */
    protected $information;

    /**
     * @param FundingSource[] $fundingSources
     */
    public function __construct(array $fundingSources = [], ?ListInformation $information = null)
    {
        $this->fundingSources = $fundingSources;
        $this->information = $information;
    }

    /**
     * @return FundingSource[]
     */
    public function getFundingSources(): array
    {
        return $this->fundingSources;
    }

    /**
     * @return ListInformation|null
     */
    public function getInformation(): ?ListInformation
    {
        return $this->information;
    }

    /**
     * @param ListInformation|null $information
     *
     * @return FundingSourceList
     */
    public function setInformation(?ListInformation $information): FundingSourceList
    {
        $this->information = $information;

        return $this;
    }
}
