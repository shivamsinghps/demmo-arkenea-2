<?php

/**
 * Created by Karina Kalina
 * Date: 24.04.18
 * Time: 14:57
 */

namespace FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\DomainBundle\Type\Campaign\Book\Course;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Course as NebookCourse;

class CourseMapper
{
    public static function map(NebookCourse $source) : Course
    {
        $result = new Course();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("id");
        $resultWrapper->realName = $sourceWrapper->getString("name");
        $resultWrapper->name = $sourceWrapper->getString("description") ?:
            ($sourceWrapper->getString("name") ?: Course::UNKNOWN_NAME);

        return $result;
    }
}
