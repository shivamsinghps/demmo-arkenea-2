<?php

namespace Tests\FMT\InfrastructureBundle\Assumptions\Nebook\Traits;

use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\{Address,
    BookInfo,
    Campus,
    Course,
    Department,
    Material,
    Product,
    ProductFamily,
    ProductSearchItem,
    ProductSearchResult,
    Requirement,
    Section,
    ShippingCode,
    Shopper,
    Tender,
    Term};

/**
 * Trait ModelValidatorTrait
 * @package Tests\FMT\InfrastructureBundle\Assumptions\Nebook\Traits
 */
trait ModelValidatorTrait
{
    /**
     * @param $needle
     * @param array $haystack
     * @return bool
     */
    private function containsById($needle, array $haystack)
    {
        foreach ($haystack as $straw) {
            if (get_class($straw) === get_class($needle) && $straw->getId() === $needle->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $value
     * @return bool
     */
    private function intAsString($value)
    {
        return (string)(int)$value === $value;
    }

    /**
     * @param Campus $campus
     */
    private function validateCampus(Campus $campus)
    {
        $this->assertNotEmpty($campus->getId());
        $this->assertInternalType('int', $campus->getId());

        $this->assertNotEmpty($campus->getName());
        $this->assertInternalType('string', $campus->getName());
    }

    /**
     * @param Tender $tender
     */
    private function validateTender(Tender $tender)
    {
        $this->assertNotEmpty($tender->getId());
        $this->assertInternalType('int', $tender->getId());

        // Term with ID#249 has "0" BackofficeId
        // $this->assertNotEmpty($tender->getBackofficeId());

        $this->assertNotEmpty($tender->getName());
        $this->assertInternalType('string', $tender->getName());

        $this->assertInternalType('bool', $tender->isIsCreditCard());
        $this->assertInternalType('bool', $tender->isIsDisabled());
        $this->assertInternalType('bool', $tender->isIsPromptRequired());
        $this->assertInternalType('bool', $tender->isIsRentalRequired());

        $this->assertInternalType('string', $tender->getPrompt());

        $this->assertInternalType('bool', $tender->isValidateBalance());
    }

    /**
     * @param Term $term
     */
    private function validateTerm(Term $term)
    {
        $this->assertNotEmpty($term->getId());
        $this->assertInternalType('int', $term->getId());

        $this->assertNotEmpty($term->getName());
        $this->assertInternalType('string', $term->getName());

        $this->assertNotEmpty($term->getCampus());
        $this->assertInstanceOf(Campus::class, $term->getCampus());

        $this->assertInternalType('int', $term->getStatus());
    }

    /**
     * @param Department $department
     * @param bool $full
     */
    private function validateDepartment(Department $department, $full = false)
    {
        $this->assertNotEmpty($department->getId());
        $this->assertInternalType('int', $department->getId());

        $this->assertNotEmpty($department->getName());
        $this->assertInternalType('string', $department->getName());

        if ($full) {
            $this->assertNotEmpty($department->getCampus());
            $this->assertInstanceOf(Campus::class, $department->getCampus());

            $courses = $department->getCourses();
            $this->assertNotEmpty($courses);
            $this->assertInstanceOf(Course::class, $courses[0]);
        }
    }

    /**
     * @param Course $course
     */
    private function validateCourse(Course $course)
    {
        $this->assertNotEmpty($course->getId());
        $this->assertInternalType('int', $course->getId());

        $this->assertNotEmpty($course->getTermId());
        $this->assertInternalType('int', $course->getTermId());

        $this->assertNotEmpty($course->getName());

        $department = $course->getDepartment();

        $this->assertThat($department,  $this->logicalOr(
            $this->isNull(),
            $this->isInstanceOf(Department::class)
        ));

        $sections = $course->getSections();
        if (!empty($sections)) {
            $this->assertInstanceOf(Section::class, $sections[0]);
        }
    }

    /**
     * @param Section $section
     */
    private function validateSection(Section $section)
    {
        $this->assertNotEmpty($section->getId());
        $this->assertInternalType('int', $section->getId());

        $this->assertNotEmpty($section->getName());
        $this->assertInternalType('string', $section->getName());

        $this->assertNotEmpty($section->getInstructorName());
        $this->assertInternalType('string', $section->getInstructorName());

        $this->assertThat($section->getCourse(), $this->logicalOr(
            $this->isNull(),
            $this->isInstanceOf(Course::class)
        ));

        $materials = $section->getMaterials();
        if (!is_null($materials)) {
            $this->assertNotEmpty($materials);
            $this->assertInstanceOf(Material::class, $materials[0]);
        }
    }

    /**
     * @param Material $material
     */
    private function validateMaterial(Material $material)
    {
        $this->assertInternalType('bool', $material->isNewOnly());
        $this->assertInternalType('bool', $material->isRentOnly());

        $this->assertThat($material->getQuantity(), $this->logicalOr(
            $this->isNull(),
            $this->isType('int')
        ));

        $this->assertInstanceOf(ProductFamily::class, $material->getFamily());
        $this->assertInstanceOf(Requirement::class, $material->getRequirement());
    }

    /**
     * @param ProductFamily $productFamily
     */
    private function validateProductFamily(ProductFamily $productFamily)
    {
        $this->assertNotEmpty($productFamily->getId());
        $this->assertTrue($this->intAsString($productFamily->getId()));

        $this->assertNotEmpty($productFamily->getName());

        $this->assertInternalType('bool', $productFamily->isReservable());

        $products = $productFamily->getProducts();
        $this->assertThat($products[0], $this->logicalOr(
            $this->isNull(),
            $this->isInstanceOf(Product::class)
        ));

        $this->assertThat($productFamily->getInfo(), $this->logicalOr(
            $this->isNull(),
            $this->isInstanceOf(BookInfo::class)
        ));
    }

    /**
     * @param Product $product
     */
    private function validateProduct(Product $product)
    {
        $this->assertNotEmpty($product->getSku());
        $this->assertInternalType('string', $product->getSku());

        $this->assertInternalType('array', $product->getAttributes());

        if (!is_null($product->getInventory())) {
            $this->assertInternalType('int', $product->getInventory());
        }

        $this->assertInternalType('int', $product->getCalculatedInventory());

        $this->assertNotEmpty($product->getPrice());
        $this->assertInternalType('int', $product->getPrice());

        $this->assertNotEmpty($product->getListPrice());
        $this->assertInternalType('int', $product->getListPrice());

        $this->assertNotEmpty($product->getAccountingCost());
        $this->assertInternalType('int', $product->getAccountingCost());

        $this->assertInternalType('int', $product->getShippingCostOverrideAmount());

//        Can be Null
//        $this->assertInstanceOf(\DateTime::class, $product->getSaleStart());
//        $this->assertInstanceOf(\DateTime::class, $product->getSaleEnd());

        $this->assertTrue(is_null($product->getOnOrder()) || $this->intAsString($product->getOnOrder()));
    }

    /**
     * @param ProductSearchResult $productSearchResult
     */
    private function validateProductSearchResult(ProductSearchResult $productSearchResult)
    {
        $this->assertInternalType('int', $productSearchResult->getPage());
        $this->assertInternalType('int', $productSearchResult->getPageSize());
        $this->assertInternalType('int', $productSearchResult->getTotalPages());
        $this->assertInternalType('int', $productSearchResult->getTotalCount());

        $this->assertNotEmpty($productSearchResult->getSearchText());
        $this->assertInternalType('string', $productSearchResult->getSearchText());
    }

    /**
     * @param ProductSearchItem $productSearchItem
     */
    private function validateProductSearchItem(ProductSearchItem $productSearchItem)
    {
        $this->assertNotEmpty($productSearchItem->getId());
        $this->assertTrue($this->intAsString($productSearchItem->getId()));

        $this->assertNotEmpty($productSearchItem->getType());
        $this->assertInternalType('int', $productSearchItem->getType());

        $this->assertNotEmpty($productSearchItem->getName());
        $this->assertInternalType('string', $productSearchItem->getName());

        $this->assertInternalType('string', $productSearchItem->getDescription());

        $this->assertNotEmpty($productSearchItem->getImageThumbnail());
        $this->assertInternalType('string', $productSearchItem->getImageThumbnail());

        $this->assertInternalType('int', $productSearchItem->getQuantity());
        $this->assertInternalType('int', $productSearchItem->getMinPrice());
        $this->assertInternalType('int', $productSearchItem->getMaxPrice());

        $this->assertGreaterThan(0, $productSearchItem->getMaxPrice());

        $this->assertThat($productSearchItem->getCatalogId(), $this->logicalOr(
            $this->isNull(),
            $this->isType('int')
        ));

        $this->assertThat($productSearchItem->getCatalogName(), $this->logicalOr(
            $this->isNull(),
            $this->isType('string')
        ));
    }

    /**
     * @param ShippingCode $shippingCode
     */
    private function validateShippingCode(ShippingCode $shippingCode)
    {
        $this->assertNotEmpty($shippingCode->getId());
        $this->assertInternalType('int', $shippingCode->getId());

        $this->assertNotEmpty($shippingCode->getName());
        $this->assertInternalType('string', $shippingCode->getName());
    }

    /**
     * @param Shopper $shopper1
     * @param Shopper $shopper2
     */
    private function isSameShopper(Shopper $shopper1, Shopper $shopper2)
    {
        $this->assertSame($shopper1->getStudentId(), $shopper2->getStudentId());
        $this->assertSame($shopper1->getEmail(), $shopper2->getEmail());
        $this->assertSame($shopper1->isAllowBuybackEmail(), $shopper2->isAllowBuybackEmail());
        $this->assertSame($shopper1->isAllowDirectEmail(), $shopper2->isAllowDirectEmail());
        $this->isSameAddress($shopper1->getBillingAddress(), $shopper2->getBillingAddress());
        $this->assertSame($shopper1->isDisabled(), $shopper2->isDisabled());
        $this->assertSame($shopper1->getMembershipId(), $shopper2->getMembershipId());
        $this->isSameAddress($shopper1->getShippingAddress(), $shopper2->getShippingAddress());
        $this->assertSame($shopper1->isTaxExempt(), $shopper2->isTaxExempt());
    }

    /**
     * @param Address $address1
     * @param Address $address2
     */
    private function isSameAddress(Address $address1, Address $address2)
    {
        $this->assertSame($address1->getAddress1(), $address2->getAddress1());
        if (!empty($address1->getAddress2()) && !empty($address2->getAddress2())) {
            $this->assertSame($address1->getAddress2(), $address2->getAddress2());
        }
        $this->assertSame($address1->getCity(), $address2->getCity());
        $this->assertSame($address1->getCountry(), $address2->getCountry());
        $this->assertSame($address1->getPhone(), $address2->getPhone());
        $this->assertSame($address1->getState(), $address2->getState());
        $this->assertSame($address1->getZip(), $address2->getZip());
        $this->assertSame($address1->getFirstName(), $address2->getFirstName());
        $this->assertSame($address1->getLastName(), $address2->getLastName());
    }
}
