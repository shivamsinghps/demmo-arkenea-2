<?php

/**
 * Created by Karina Kalina
 * Date: 24.04.18
 * Time: 14:57
 */

namespace FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\DomainBundle\Type\Campaign\Book\Section;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Section as NebookSection;

class SectionMapper
{
    public static function map(NebookSection $source) : Section
    {
        $result = new Section();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("id");

        $name = $sourceWrapper->getString("instructorName");
        if ($sourceWrapper->getString("name")) {
            $name .= sprintf(': %s', $sourceWrapper->getString("name"));
        }

        $resultWrapper->name = $name;

        return $result;
    }
}
