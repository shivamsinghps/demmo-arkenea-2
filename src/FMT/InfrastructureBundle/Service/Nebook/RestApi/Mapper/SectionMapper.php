<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:05
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Course;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Material;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Section;

class SectionMapper extends AbstractMapper
{
    public function map(array $source) : Section
    {
        $result = new Section();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->registrationNumber = $sourceWrapper->getString("RegistrationNumber");
        $resultWrapper->instructorEmail = $sourceWrapper->getString("InstructorEmail");
        $resultWrapper->instructorName = $sourceWrapper->getString("InstructorName");
        $resultWrapper->estimatedEnrollment = $sourceWrapper->getInt("EstimatedEnrollment");

        if ($course = $sourceWrapper->get("Course")) {
            $resultWrapper->course = $this->mapper->map($course, Course::class);
        }

        if ($materials = $sourceWrapper->get("CourseMaterials")) {
            $resultWrapper->materials = array_map(function ($item) {
                return $this->mapper->map($item, Material::class);
            }, $materials);
        }

        return $result;
    }
}
