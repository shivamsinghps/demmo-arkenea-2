<?php
/**
 * Author: Anton Orlov
 * Date: 27.02.2018
 * Time: 17:50
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Tender
{
    /** @var int */
    private $id;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $isCreditCard;

    /** @var bool */
    private $isDisabled;

    /** @var bool */
    private $isPromptRequired;

    /** @var bool */
    private $isRentalRequired;

    /** @var string */
    private $prompt;

    /** @var string */
    private $regexPattern;

    /** @var int */
    private $sortOrder;

    /** @var bool */
    private $validateBalance;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBackofficeId()
    {
        return $this->backofficeId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function isIsCreditCard()
    {
        return $this->isCreditCard;
    }

    /**
     * @return boolean
     */
    public function isIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * @return boolean
     */
    public function isIsPromptRequired()
    {
        return $this->isPromptRequired;
    }

    /**
     * @return boolean
     */
    public function isIsRentalRequired()
    {
        return $this->isRentalRequired;
    }

    /**
     * @return string
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * @return string
     */
    public function getRegexPattern()
    {
        return $this->regexPattern;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @return boolean
     */
    public function isValidateBalance()
    {
        return $this->validateBalance;
    }
}
