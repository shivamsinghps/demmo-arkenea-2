<?php
/**
 * Author: Anton Orlov
 * Date: 27.02.2018
 * Time: 14:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi;

use FMT\InfrastructureBundle\Helper\ArrayHelper;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\AddItemsResult;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartItem;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartSummary;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Course;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Department;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Material;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OperationResult;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Order;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\PaymentInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Product;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductFamily;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductSearchResult;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Section;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingCode;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Shopper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\SubmitOrderResult;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Tender;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Term;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Transport\NotFoundException;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Transport\TransportInterface;

/**
 * Class Client
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Client
{
    const DEFAULT_PAGE_SIZE = 50;

    /** @var TransportInterface */
    private $transport;

    /** @var Mapper */
    private $mapper;

    /**
     * Client constructor.
     * @param TransportInterface $transport
     */
    public function __construct(TransportInterface $transport, Mapper $mapper)
    {
        $this->transport = $transport;
        $this->mapper = $mapper;
    }

    /**
     * Method returns available payment methods of Nebook
     *
     * @return Tender[]
     * @throws Exception
     */
    public function tendersGetAll()
    {
        $response = $this->transport->get("TendersGetAll");

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Tender::class);
        }, $response);
    }

    /**
     * Method returns information about specific payment method by payment method ID
     *
     * @param int $tenderId
     * @return Tender
     */
    public function tenderGetById($tenderId)
    {
        $response = $this->transport->get("TenderGetById", ["id" => $tenderId]);

        return $this->mapper->map($response, Tender::class);
    }

    /**
     * Method returns list of terms from Nebook
     *
     * @return Term[]
     * @throws Exception
     */
    public function termsGetAll()
    {
        $response = $this->transport->get("TermsGetAll");

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Term::class);
        }, $response);
    }

    /**
     * Method returns list of terms from Nebook
     *
     * @return Term[]
     * @throws Exception
     */
    public function termsGetOpened()
    {
        $terms = $this->termsGetAll();

        return array_values(array_filter($terms, function (Term $item) {
            return $item->getAdoptionStatus() == Term::ADOPTION_STATUS_OPEN && $item->getStatus() == Term::STATUS_OPEN;
        }));
    }

    /**
     * Method returns specific term by term ID
     *
     * @param int $termId
     * @return Term
     */
    public function termGetById($termId)
    {
        $response = $this->transport->get("TermGetById", ["termId" => $termId]);

        return $this->mapper->map($response, Term::class);
    }

    /**
     * Method returns list of departments by term ID
     *
     * @param int $termId
     * @return Department[]
     * @throws Exception
     */
    public function departmentsGetByTermId($termId)
    {
        $response = $this->transport->get("DepartmentsGetByTermId", ["termId" => $termId]);

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Department::class);
        }, $response);
    }

    /**
     * Method returns list of active departments by term ID
     *
     * @param int $termId
     * @return Department[]
     * @throws Exception
     */
    public function departmentsGetActiveByTermId($termId)
    {
        $response = $this->transport->get("DepartmentsGetActiveByTermId", ["termId" => $termId]);

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Department::class);
        }, $response);
    }

    /**
     * Method returns information about specific department by department ID
     *
     * @param int $departmentId
     * @return Department
     */
    public function departmentGetById($departmentId)
    {
        $response = $this->transport->get("DepartmentGetbyId", ["departmentId" => $departmentId]);

        return $this->mapper->map($response, Department::class);
    }

    /**
     * Method returns list of courses by department and term ID
     *
     * @param int $departmentId
     * @param int $termId
     * @return Course[]
     * @throws Exception
     */
    public function coursesGetByDepartmentIdAndTermId($departmentId, $termId)
    {
        $response = $this->transport->get("CoursesGetByDepartmentIdAndTermId", [
            "departmentId" => $departmentId,
            "termId" => $termId,
        ]);

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Course::class);
        }, $response);
    }

    /**
     * Method returns list of sections by course ID
     *
     * @param int $courseId
     * @return Section[]
     * @throws Exception
     */
    public function sectionsGetByCourseId($courseId)
    {
        $response = $this->transport->get("SectionsGetByCourseId", ["courseId" => $courseId]);

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Section::class);
        }, $response);
    }

    /**
     * Method returns list of sections by list of section ID
     *
     * @param int[] $sectionIds
     * @return Section[]
     * @throws Exception
     */
    public function sectionsGetBySectionIds($sectionIds)
    {
        $response = $this->transport->get("SectionsGetBySectionIds", join(",", $sectionIds));

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Section::class);
        }, $response);
    }

    /**
     * Method returns information about section by section ID
     *
     * @param int $sectionId
     * @return Section
     */
    public function sectionGetById($sectionId)
    {
        $response = $this->transport->get("SectionGetById", ["sectionId" => $sectionId]);

        return $this->mapper->map($response, Section::class);
    }

    /**
     * Method returns list of materials by section ID
     *
     * @param int $sectionId
     * @return Material[]
     * @throws Exception
     */
    public function courseMaterialsGetBySectionId($sectionId)
    {
        $response = $this->transport->get("CourseMaterialsGetBySectionId", ["sectionId" => $sectionId]);

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Material::class);
        }, $response);
    }

    /**
     * Method returns list of products by product family ID
     *
     * @param string $productFamilyId
     * @return Product[]
     * @throws Exception
     */
    public function productsGetByProductFamilyId($productFamilyId)
    {
        $response = $this->transport->get("ProductsGetByProductFamilyId", (string)$productFamilyId);

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Product::class);
        }, $response);
    }

    /**
     * Method returns detailed information about product family by ID
     *
     * @param string $productFamilyId
     * @return ProductFamily
     */
    public function productFamilyGetById($productFamilyId)
    {
        try {
            $response = $this->transport->get("ProductFamilyGetById", (string)$productFamilyId);
        } catch (NotFoundException $notFoundException) {
            $response = [];
        }

        return $this->mapper->map($response, ProductFamily::class);
    }

    /**
     * Method searches product family by ISBN identifier and term ID (optional)
     *
     * @param string $isbn
     * @param int $termId
     * @return ProductFamily
     */
    public function productFamilyGetByIsbn($isbn, $termId = null)
    {
        $args = [$isbn];
        if (!empty($termId)) {
            $args[] = $termId;
        }

        try {
            $response = $this->transport->get("ProductFamilyGetByIsbn", $args);
        } catch (NotFoundException $notFoundException) {
            $response = [];
        }

        return $this->mapper->map($response, ProductFamily::class);
    }

    /**
     * Method returns detailed info about product by product family ID and SKU
     *
     * @param string $productFamilyId
     * @param string $sku
     * @return Product
     */
    public function productGetByProductFamilyIdAndSku($productFamilyId, $sku)
    {
        $response = $this->transport->get("ProductGetByProductFamilyIdAndSku", [
            "productFamilyId" => $productFamilyId,
            "sku" => $sku,
        ]);

        return $this->mapper->map($response, Product::class);
    }

    /**
     * Method search product families by text pattern and returns search results
     *
     * @param string $text
     * @param int $page
     * @param int $size
     * @return ProductSearchResult
     */
    public function productIndexedSearch($text, $page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        $response = $this->transport->get("ProductIndexedSearch", [
            "searchText" => $text,
            "page" => $page,
            "pageSize" => $size,
        ]);

        return $this->mapper->map($response, ProductSearchResult::class);
    }

    /**
     * Method searches product family by text pattern and returns iterator to walk through search results
     *
     * @param string $text
     * @return \Generator
     */
    public function productIndexedSearchIterable($text)
    {
        $page = 0;
        do {
            $result = $this->productIndexedSearch($text, $page);
            foreach ($result->getSearchResults() as $item) {
                yield $item;
            }
            $page++;
        } while ($page < $result->getTotalPages());
    }

    /**
     * Method creates new shopper
     *
     * @param Shopper $shopper
     * @return bool
     * @throws Exception
     */
    public function shopperCreate(Shopper $shopper)
    {
        $response = $this->transport->post("ShopperCreate", $this->mapper->map($shopper, "array"));
        /** @var OperationResult $result */
        $result = $this->mapper->map($response, OperationResult::class);
        if (!$result->isSuccess()) {
            throw new Exception(sprintf("[%s] %s", __METHOD__, $result->getMessage()));
        }

        return $result->getMessage();
    }

    /**
     * Method updates shopper in the system
     *
     * @param string $shopperId
     * @param Shopper $shopper
     * @return bool
     * @throws Exception
     */
    public function shopperUpdate($shopperId, Shopper $shopper)
    {
        $response = $this->transport->post("ShopperUpdate", $this->mapper->map($shopper, "array"), $shopperId);
        /** @var OperationResult $result */
        $result = $this->mapper->map($response, OperationResult::class);
        if (!$result->isSuccess()) {
            throw new Exception(sprintf("[%s] %s", __METHOD__, $result->getMessage()));
        }

        return $result->isSuccess();
    }

    /**
     * Method disables shopper account by shopper ID
     *
     * @param string $shopperId
     * @return bool
     * @throws Exception
     */
    public function shopperDisableById($shopperId)
    {
        $response = $this->transport->post("ShopperDisableById", (string)$shopperId);
        /** @var OperationResult $result */
        $result = $this->mapper->map($response, OperationResult::class);
        if (!$result->isSuccess()) {
            throw new Exception(sprintf("[%s] %s", __METHOD__, $result->getMessage()));
        }

        return $result->isSuccess();
    }

    /**
     * Method returns information about shopper by shopper ID
     *
     * @param string $shopperId
     * @return Shopper
     */
    public function shopperGetById($shopperId)
    {
        $response = $this->transport->get("ShopperGetById", (string)$shopperId);

        return $this->mapper->map($response, Shopper::class);
    }

    /**
     * Method returns information about shopper by shopper email
     *
     * @param string $email
     * @return Shopper
     */
    public function shopperGetByEmail($email)
    {
        $response = $this->transport->get("ShopperGetByEmail", (string)$email);

        return $this->mapper->map($response, Shopper::class);
    }

    /**
     * Method cleans up shopper's cart
     *
     * @param string $shopperId
     * @return bool
     * @throws Exception
     */
    public function clearCart($shopperId)
    {
        $response = $this->transport->post("ClearCart", null, (string)$shopperId);
        /** @var OperationResult $result */
        $result = $this->mapper->map($response, OperationResult::class);
        if (!$result->isSuccess()) {
            throw new Exception(sprintf("[%s] %s", __METHOD__, $result->getMessage()));
        }

        return $result->isSuccess();
    }

    /**
     * Method adds list of items to shopper's cart
     *
     * @param string $shopperId
     * @param CartItem[] $items
     * @return CartSummary
     */
    public function cartAddItems($shopperId, $items)
    {
        $response = $this->transport->post("CartAddItems", $this->mapper->mapList($items, "array"), (string)$shopperId);
        /** @var AddItemsResult $result */
        $result = $this->mapper->map($response, AddItemsResult::class);

        return $result->getSummary();
    }


    /**
     * @param $shopperId
     * @return CartSummary
     */
    public function cartGetSummary($shopperId)
    {
        $response = $this->transport->get("CartGetSummary", ['shopperId' => (string) $shopperId]);
        return $this->mapper->map($response, CartSummary::class);
    }

    /**
     * Method adds shipping info to shopping cart
     *
     * @param string $shopperId
     * @param ShippingInfo $shipping
     * @return CartSummary
     * @throws Exception
     */
    public function checkoutAddShipping($shopperId, ShippingInfo $shipping)
    {
        $mappedShipping = $this->mapper->map($shipping, "array");
        $response = $this->transport->post("CheckoutAddShipping", $mappedShipping, (string)$shopperId);
        /** @var OperationResult $result */
        $result = $this->mapper->map($response, OperationResult::class);
        if (!$result->isSuccess()) {
            throw new Exception(sprintf("[%s] %s", __METHOD__, $result->getMessage()));
        }

        if ($summary = $result->getAdvanced("Cart")) {
            return $this->mapper->map($summary, CartSummary::class);
        }

        return null;
    }

    /**
     * Method adds payment info to shopping cart
     *
     * @param string $shopperId
     * @param PaymentInfo $payment
     * @return CartSummary
     * @throws Exception
     */
    public function checkoutAddPayment($shopperId, PaymentInfo $payment)
    {
        $mappedPayment = $this->mapper->map($payment, "array");
        $response = $this->transport->post("CheckoutAddPayment", $mappedPayment, (string)$shopperId);
        /** @var OperationResult $result */
        $result = $this->mapper->map($response, OperationResult::class);
        if (!$result->isSuccess()) {
            throw new Exception(sprintf("[%s] %s", __METHOD__, $result->getMessage()));
        }

        if ($summary = $result->getAdvanced("Cart")) {
            return $this->mapper->map($summary, CartSummary::class);
        }

        return null;
    }

    /**
     * Method checks out current cart
     *
     * @param string $shopperId
     * @return Order
     */
    public function checkoutSubmitOrder($shopperId)
    {
        $response = $this->transport->post("CheckoutSubmitOrder", null, (string)$shopperId);
        /** @var SubmitOrderResult $result */
        $result = $this->mapper->map($response, SubmitOrderResult::class);

        return $result->getOrder();
    }

    /**
     * Method returns information about cart of the shopper
     *
     * @param string $shopperId
     * @return CartSummary
     * @throws Exception
     */
    public function checkoutVerifyOrder($shopperId)
    {
        $response = $this->transport->get("CheckoutVerifyOrder", (string)$shopperId);
        /** @var OperationResult $result */
        $result = $this->mapper->map($response, OperationResult::class);
        if (!$result->isSuccess()) {
            throw new Exception(sprintf("[%s] %s", __METHOD__, $result->getMessage()));
        }

        if ($summary = $result->getAdvanced("Cart")) {
            return $this->mapper->map($summary, CartSummary::class);
        }

        return null;
    }

    /**
     * Method returns detailed info about order by order ID
     *
     * @param int $orderId
     * @return Order
     */
    public function orderGetById($orderId)
    {
        $response = $this->transport->get("OrderGetById", ["orderId" => (int)$orderId]);

        return $this->mapper->map($response, Order::class);
    }

    /**
     * Method returns detailed info about order by unique order ID of order
     *
     * @param string $orderUniqueId
     * @return Order
     */
    public function orderGetByUniqueId($orderUniqueId)
    {
        $response = $this->transport->get("OrderGetByUniqueId", ["uniqueOrderId" => (string)$orderUniqueId]);

        return $this->mapper->map($response, Order::class);
    }

    /**
     * Method returns list of departments by campus ID
     *
     * @param $campusId
     * @return array
     * @throws Exception
     */
    public function departmentsGetByCampusId($campusId)
    {
        $response = $this->transport->get("DepartmentsGetByCampusId", ["campusId" => $campusId]);

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, Department::class);
        }, $response);
    }

    /**
     * Method returns list of shipping options
     *
     * @return array
     * @throws Exception
     */
    public function shippingCodesGetAll()
    {
        $response = $this->transport->get("ShippingCodesGetAll");

        if (!ArrayHelper::isArray($response)) {
            throw new Exception("Unexpected response data format");
        }

        return array_map(function ($item) {
            return $this->mapper->map($item, ShippingCode::class);
        }, $response);
    }
}
