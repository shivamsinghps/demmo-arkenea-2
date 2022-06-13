<?php

namespace Tests\FMT\InfrastructureBundle\Assumptions\Nebook;

use FMT\InfrastructureBundle\Helper\ArrayHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\{Course,
    Department,
    Material,
    Product,
    ProductFamily,
    ProductSearchResult,
    Section,
    ShippingCode,
    Tender,
    Term};
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Exception;
use Tests\FMT\InfrastructureBundle\AbstractTest;
use Tests\FMT\InfrastructureBundle\Assumptions\Nebook\Traits\CartHelperTrait;
use Tests\FMT\InfrastructureBundle\Assumptions\Nebook\Traits\ModelValidatorTrait;

/**
 * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help
 *
 * Class CatalogApiTest
 * @package Tests\FMT\InfrastructureBundle\Assumptions
 */
class CatalogApiTest extends AbstractTest
{
    use ModelValidatorTrait, CartHelperTrait;

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/TendersGetAll
     *
     * @return Tender[]
     */
    public function testTendersGetAll()
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultTendersGetAll = $client->tendersGetAll();

        $this->assertNotEmpty($resultTendersGetAll);
        $this->assertTrue(ArrayHelper::isArray($resultTendersGetAll));

        foreach ($resultTendersGetAll as $tender) {
            $this->assertInstanceOf(Tender::class, $tender);
            $this->validateTender($tender);
        }

        return $resultTendersGetAll;
    }

    /**
     * @depends testTendersGetAll
     *
     * @param array $tendersGetAll
     * @return Tender
     */
    public function testTenderGetById(array $tendersGetAll)
    {
        $tender = $tendersGetAll[0];

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $tender = $client->tenderGetById($tender->getId());

        $this->assertInstanceOf(Tender::class, $tender);
        $this->validateTender($tender);

        return $tender;
    }

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/TendersGetAll
     *
     * @return Term[]
     */
    public function testTermsGetOpened()
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $terms = $client->termsGetOpened();

        $this->assertNotEmpty($terms);
        $this->assertTrue(ArrayHelper::isArray($terms));

        foreach ($terms as $term) {
            $this->assertInstanceOf(Term::class, $term);
            $this->validateTerm($term);
            $this->validateCampus($term->getCampus());
        }

        return $terms;
    }

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/DepartmentsGetByTermId
     *
     * @depends testTermsGetOpened
     *
     * @param array $termsGetOpened
     * @return Department[]
     */
    public function testDepartmentsGetByTermId(array $termsGetOpened)
    {
        $term = $termsGetOpened[0];

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultDepartmentsGetByTermId = $client->departmentsGetByTermId($term->getId());

        $this->assertNotEmpty($resultDepartmentsGetByTermId);
        $this->assertTrue(ArrayHelper::isArray($resultDepartmentsGetByTermId));

        foreach ($resultDepartmentsGetByTermId as $department) {
            $this->assertInstanceOf(Department::class, $department);
            $this->validateDepartment($department, true);

            $this->validateCampus($department->getCampus());

            foreach ($department->getCourses() as $course) {
                $this->validateCourse($course);
            }
        }

        return $resultDepartmentsGetByTermId;
    }

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/DepartmentsGetActiveByTermId
     *
     * @depends testTermsGetOpened
     * @depends testDepartmentsGetByTermId
     *
     * @param array $termsGetOpened
     * @param array $departmentsGetByTermId
     * @return Department[]
     */
    public function testDepartmentsGetActiveByTermId(array $termsGetOpened, array $departmentsGetByTermId)
    {
        $term = $termsGetOpened[0];

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultDepartmentsGetActiveByTermId = $client->departmentsGetActiveByTermId($term->getId());

        $this->assertNotEmpty($resultDepartmentsGetActiveByTermId);
        $this->assertTrue(ArrayHelper::isArray($resultDepartmentsGetActiveByTermId));

        foreach ($resultDepartmentsGetActiveByTermId as $department) {
            $this->assertInstanceOf(Department::class, $department);
            $this->validateDepartment($department);
            $this->assertTrue($this->containsById($department, $departmentsGetByTermId));
        }

        return $resultDepartmentsGetActiveByTermId;
    }

    /**
     * @depends testDepartmentsGetByTermId
     *
     * @param array $departmentsGetByTermId
     * @return Department
     */
    public function testDepartmentGetById(array $departmentsGetByTermId)
    {
        $department = $departmentsGetByTermId[0];

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultDepartmentGetById = $client->departmentGetById($department->getId());

        $this->assertInstanceOf(Department::class, $resultDepartmentGetById);
        $this->validateDepartment($resultDepartmentGetById, true);
        $this->assertTrue($this->containsById($resultDepartmentGetById, $departmentsGetByTermId));

        $this->validateCampus($department->getCampus());

        foreach ($resultDepartmentGetById->getCourses() as $course) {
            $this->validateCourse($course);
        }

        return $resultDepartmentGetById;
    }

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/CoursesGetByDepartmentIdAndTermId
     *
     * @depends testTermsGetOpened
     * @depends testDepartmentsGetActiveByTermId
     *
     * @param array $termsGetOpened
     * @param array $departmentsGetActiveByTermId
     * @return Course[]
     */
    public function testCoursesGetByDepartmentIdAndTermId(array $termsGetOpened, array $departmentsGetActiveByTermId)
    {
        $term = $termsGetOpened[0];
        $department = $departmentsGetActiveByTermId[0];

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultCoursesGetByDepartmentIdAndTermId = $client->coursesGetByDepartmentIdAndTermId(
            $department->getId(),
            $term->getId()
        );

        $this->assertNotEmpty($resultCoursesGetByDepartmentIdAndTermId);
        $this->assertTrue(ArrayHelper::isArray($resultCoursesGetByDepartmentIdAndTermId));

        foreach ($resultCoursesGetByDepartmentIdAndTermId as $course) {
            $this->assertInstanceOf(Course::class, $course);
            $this->validateCourse($course);

            if (!is_null($course->getSections())) {
                foreach ($course->getSections() as $section) {
                    $this->validateSection($section);

                    if (!is_null($section->getCourse())) {
                        $this->validateCourse($section->getCourse());
                    }

                    if (!is_null($section->getMaterials())) {
                        foreach ($section->getMaterials() as $material) {
                            $this->validateMaterial($material);
                        }
                    }
                }
            }
        }

        return $resultCoursesGetByDepartmentIdAndTermId;
    }

    /**
     * @depends testCoursesGetByDepartmentIdAndTermId
     *
     * @param array $coursesGetByDepartmentIdAndTermId
     * @return Section[]
     */
    public function testSectionsGetByCourseId(array $coursesGetByDepartmentIdAndTermId)
    {
        $course = $coursesGetByDepartmentIdAndTermId[0];
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultSectionsGetByCourseId = $client->sectionsGetByCourseId($course->getId());

        $this->assertNotEmpty($resultSectionsGetByCourseId);
        $this->assertTrue(ArrayHelper::isArray($resultSectionsGetByCourseId));

        foreach ($resultSectionsGetByCourseId as $section) {
            $this->assertInstanceOf(Section::class, $section);
            $this->validateSection($section);

            $this->validateCourse($section->getCourse());

            foreach ($section->getMaterials() as $material) {
                $this->validateMaterial($material);
                $this->validateProductFamily($material->getFamily());

                foreach ($material->getFamily()->getProducts() as $product) {
                    $this->validateProduct($product);
                }
            }
        }

        return $resultSectionsGetByCourseId;
    }

    /**
     * @depends testSectionsGetByCourseId
     *
     * @param array $sectionsGetByCourseId
     * @return Section[]
     */
    public function testSectionsGetBySectionIds(array $sectionsGetByCourseId)
    {
        $sectionIds = array_map(
            function (Section $section) {
                return $section->getId();
            },
            $sectionsGetByCourseId
        );

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultSectionsGetBySectionIds = $client->sectionsGetBySectionIds($sectionIds);

        $this->assertNotEmpty($resultSectionsGetBySectionIds);
        $this->assertTrue(ArrayHelper::isArray($resultSectionsGetBySectionIds));

        $this->assertCount(count($sectionsGetByCourseId), $resultSectionsGetBySectionIds);

        $resultSectionIds = array_map(function (Section $section) {
            return $section->getId();
        }, $resultSectionsGetBySectionIds);

        $this->assertEquals(array_values($sectionIds), array_values($resultSectionIds));

        foreach ($resultSectionsGetBySectionIds as $section) {
            $this->assertInstanceOf(Section::class, $section);
            $this->validateSection($section);

            $this->validateCourse($section->getCourse());

            foreach ($section->getMaterials() as $material) {
                $this->validateMaterial($material);
                $this->validateProductFamily($material->getFamily());

                foreach ($material->getFamily()->getProducts() as $product) {
                    $this->validateProduct($product);
                }
            }
        }

        return $resultSectionsGetBySectionIds;
    }

    /**
     * @depends testSectionsGetByCourseId
     *
     * @param array $sectionsGetByCourseId
     * @return Section
     */
    public function testSectionGetById(array $sectionsGetByCourseId)
    {
        $section = $sectionsGetByCourseId[0];

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultSectionGetById = $client->sectionGetById($section->getId());

        $this->validateSection($resultSectionGetById);

        $this->assertEquals($section->getId(), $resultSectionGetById->getId());

        $this->validateCourse($resultSectionGetById->getCourse());

        foreach ($resultSectionGetById->getMaterials() as $material) {
            $this->validateMaterial($material);
            $this->validateProductFamily($material->getFamily());

            foreach ($material->getFamily()->getProducts() as $product) {
                $this->validateProduct($product);
            }
        }

        return $resultSectionGetById;
    }

    /**
     * @depends testSectionGetById
     *
     * @param Section $section
     * @return Material[]
     */
    public function testCourseMaterialsGetBySectionId(Section $section)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultCourseMaterialsGetBySectionId = $client->courseMaterialsGetBySectionId($section->getId());

        foreach ($resultCourseMaterialsGetBySectionId as $material) {
            $this->assertInstanceOf(Material::class, $material);
            $this->validateMaterial($material);

            $this->validateProductFamily($material->getFamily());

            foreach ($material->getFamily()->getProducts() as $product) {
                $this->assertInstanceOf(Product::class, $product);
                $this->validateProduct($product);
            }
        }

        return $resultCourseMaterialsGetBySectionId;
    }

    /**
     * @depends testSectionGetById
     *
     * @param Section $section
     * @return Product[]
     */
    public function testProductsGetByProductFamilyId(Section $section)
    {
        $family = $section->getMaterials()[0]->getFamily();

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultProductsGetByProductFamilyId = $client->productsGetByProductFamilyId($family->getId());

        foreach ($resultProductsGetByProductFamilyId as $product) {
            $this->validateProduct($product);
        }

        return $resultProductsGetByProductFamilyId;
    }

    /**
     * @depends testSectionGetById
     *
     * @param Section $section
     * @return ProductFamily
     */
    public function testProductFamilyGetById(Section $section)
    {
        $family = $section->getMaterials()[0]->getFamily();

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultProductFamilyGetById = $client->productFamilyGetById($family->getId());

        $this->validateProductFamily($resultProductFamilyGetById);
        $this->assertEquals($family->getId(), $resultProductFamilyGetById->getId());

        foreach ($resultProductFamilyGetById->getProducts() as $product) {
            $this->validateProduct($product);
        }

        return $resultProductFamilyGetById;
    }

    /**
     * @depends testProductFamilyGetById
     *
     * @param ProductFamily $family
     * @return Product
     */
    public function testProductGetByProductFamilyIdAndSku(ProductFamily $family)
    {
        $product = $family->getProducts()[0];

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultProductGetByProductFamilyIdAndSku = $client->productGetByProductFamilyIdAndSku(
            $family->getId(),
            $product->getSku()
        );

        $this->validateProduct($product);

        return $resultProductGetByProductFamilyIdAndSku;
    }

    /**
     * @return ProductSearchResult
     */
    public function testProductIndexedSearch()
    {
        $search = 'a';

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultProductIndexedSearch = $client->productIndexedSearch($search);

        $this->validateProductSearchResult($resultProductIndexedSearch);

        $this->assertNotEmpty($resultProductIndexedSearch->getSearchResults());

        foreach ($resultProductIndexedSearch->getSearchResults() as $searchResult) {
            $this->validateProductSearchItem($searchResult);
        }

        $needle = $resultProductIndexedSearch->getSearchResults()[2];
        $productName = $needle->getName();
        $partialName = substr($productName, 0, -2);

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultProductIndexedSearchByTitle = $client->productIndexedSearch($productName, 0, 1e5);
        $this->assertTrue($this->containsById($needle, $resultProductIndexedSearchByTitle->getSearchResults()));

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $resultProductIndexedSearchByPartialTitle = $client->productIndexedSearch($partialName, 0, 1e5);
        $this->assertTrue($this->containsById($needle, $resultProductIndexedSearchByPartialTitle->getSearchResults()));

        return $resultProductIndexedSearch;
    }

    /**
     * @depends testDepartmentsGetByTermId
     *
     * @param array $departmentsGetByTermId
     * @return Department[]
     */
    public function testDepartmentsGetByCampusId(array $departmentsGetByTermId)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');

        $department = $departmentsGetByTermId[0];
        $campusId = $department->getCampus()->getId();
        $resultDepartmentsGetByCampusId = $client->departmentsGetByCampusId($campusId);

        $this->assertNotEmpty($resultDepartmentsGetByCampusId);
        $this->assertTrue(ArrayHelper::isArray($resultDepartmentsGetByCampusId));

        foreach ($resultDepartmentsGetByCampusId as $department) {
            $this->assertInstanceOf(Department::class, $department);
            $this->validateDepartment($department);
        }

        return $resultDepartmentsGetByCampusId;
    }

    /**
     * @return ShippingCode[]
     */
    public function testShippingCodesGetAll()
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $shippingCodes = $client->shippingCodesGetAll();

        $this->assertNotEmpty($shippingCodes);
        $this->assertTrue(ArrayHelper::isArray($shippingCodes));

        foreach ($shippingCodes as $shippingCode) {
            $this->assertInstanceOf(ShippingCode::class, $shippingCode);
            $this->validateShippingCode($shippingCode);
        }

        return $shippingCodes;
    }

    /**
     * @see https://webprism.nbcservices.com/v3.14/WebPrismService.svc/json/help/operations/TermsGetAll
     */
    public function testTermsGetAll()
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $terms = $client->termsGetAll();

        $this->assertNotEmpty($terms);
        $this->assertTrue(ArrayHelper::isArray($terms));

        foreach ($terms as $term) {
            $this->assertInstanceOf(Term::class, $term);
            $this->validateTerm($term);
            $this->validateCampus($term->getCampus());
        }
    }

    /**
     * @see https://webprism.nbcservices.com/v3.14/WebPrismService.svc/json/help/operations/ProductFamilyGetByIsbn
     *
     * @throws Exception
     */
    public function testProductFamilyGetByIsbn()
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $terms = $client->termsGetOpened();
        $term = $terms[0];
        $isbn = $this->getIsbn($client, $term);

        $this->assertNotNull($isbn);
        $this->assertInstanceOf(ProductFamily::class,
            $client->productFamilyGetByIsbn($isbn, $term->getId())
        );
    }
}
