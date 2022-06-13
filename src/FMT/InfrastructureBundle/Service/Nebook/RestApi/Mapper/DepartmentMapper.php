<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 16:21
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Campus;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Course;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Department;

class DepartmentMapper extends AbstractMapper
{
    public function map(array $source) : Department
    {
        $result = new Department();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");
        $resultWrapper->abbreviation = $sourceWrapper->getString("Abbreviation");
        $resultWrapper->campus = $this->mapper->map($sourceWrapper->get("Campus"), Campus::class);
        $resultWrapper->courses = array_map(function ($item) {
            return $this->mapper->map($item, Course::class);
        }, $sourceWrapper->get("Courses") ?: []);

        return $result;
    }
}
