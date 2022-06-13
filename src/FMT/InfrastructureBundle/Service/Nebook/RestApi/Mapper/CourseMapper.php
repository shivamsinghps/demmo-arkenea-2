<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 16:13
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Course;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Department;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Section;

class CourseMapper extends AbstractMapper
{
    public function map(array $source) : Course
    {
        $result = new Course();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->termId = $sourceWrapper->getInt("TermId");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->name = $sourceWrapper->getInt("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");

        if ($department = $sourceWrapper->get("Department")) {
            $resultWrapper->department = $this->mapper->map($department, Department::class);
        }

        if ($sections = $sourceWrapper->get("Sections")) {
            $resultWrapper->sections = array_map(function ($item) {
                return $this->mapper->map($item, Section::class);
            }, $sections);
        }

        return $result;
    }
}
