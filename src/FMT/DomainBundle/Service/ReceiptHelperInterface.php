<?php

namespace FMT\DomainBundle\Service;

use FMT\DomainBundle\Service\Pdf\ReceiptDto;
use FMT\DomainBundle\Service\Pdf\ReceiptItemDto;

/**
 * Interface ReceiptHelperInterface
 * @package FMT\DomainBundle\Service
 */
interface ReceiptHelperInterface
{
    const HEADER_TITLES = [
        'description',
        'qty',
        'amount',
    ];

    const FONT_FAMILY_DEFAULT = 'Helvetica';

    const FONT_SIZE_DEFAULT = 10;
    const FONT_SIZE_TITLE = 20;
    const FONT_SIZE_TABLE_BLOCK = 10;
    const FONT_SIZE_FOOTER = 8;

    /**
     * @param ReceiptDto $info
     * @param ReceiptItemDto[]|array $items
     * @return string
     */
    public function getReceipt(ReceiptDto $info, array $items);
}
