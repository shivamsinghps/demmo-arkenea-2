<?php

namespace Tests\FMT\DomainBundle\Service\Cart;

use Doctrine\ORM\EntityManagerInterface;
use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\Cart\NebookService;
use FMT\DomainBundle\Service\Cart\Processor\NebookProcessor;
use FMT\DomainBundle\Service\CartManagerInterface;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartSummary;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\FMT\DomainBundle\AbstractDbTest;
use Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart\CampaignBookFixture;
use Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart\CampaignFixture;
use Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart\UserFixture;

/**
 * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help
 *
 * Class CartManagerTest
 * @package Tests\FMT\DomainBundle\Service\Cart
 */
class CartManagerTest extends AbstractDbTest
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private static $tokens = null;

    private static $books = null;

    public function setUp()
    {
        parent::setUp();

        $this->cartManager = $this->container->get('test.domain.service.cart.cart_manager');
        $this->tokenStorage = $this->container->get('security.token_storage');

        self::$tokens = null;
        self::$books = null;

        // default dummy values
        $cartSummaryValues = [
            'subTotal' => 2000,
            'shippingTotal' => 1000,
            'taxTotal' => 500,
        ];
        $this->mockNebookService($cartSummaryValues);
    }

    public function testCartCreation()
    {
        $authenticationData = $this->getAuthenticationData();

        foreach ($authenticationData as $data) {
            /** @var TokenInterface $token */
            $token = $data['token'];
            $expected = $data['expected'];

            $orderRepo = $this->em->getRepository(Order::class);

            $initialOrderNumber = $orderRepo->total();

            $this->initCart($token);

            $cart = $this->cartManager->get();

            $this->assertInstanceOf(Order::class, $cart);

            $this->assertEquals(0, $cart->getItems()->count());

            $this->assertEquals($expected['user'], $cart->getUser());
            $this->assertEquals($expected['anonymousTokenNotEmpty'], !empty($cart->getAnonymousToken()));

            $this->assertEquals($initialOrderNumber, $orderRepo->total());
        }
    }

    public function testHasProduct()
    {
        $authenticationData = $this->getAuthenticationData();

        foreach ($authenticationData as $data) {
            /** @var TokenInterface $token */
            $token = $data['token'];

            $this->initCart($token);

            $product1 = $this->getProduct(CampaignBookFixture::BOOK_1_TITLE);
            $this->assertFalse($this->cartManager->hasProduct($product1));

            $this->cartManager->addProduct($product1);

            $this->assertTrue($this->cartManager->hasProduct($product1));

            $product3 = $this->getProduct(CampaignBookFixture::BOOK_3_TITLE);
            $this->assertFalse($this->cartManager->hasProduct($product3));

            $this->cartManager->addProduct($product3);

            $this->assertTrue($this->cartManager->hasProduct($product1));
            $this->assertTrue($this->cartManager->hasProduct($product3));

            $this->cartManager->removeProduct($product1);
            $this->cartManager->removeProduct($product3);

            $product4 = $this->getProduct(CampaignBookFixture::BOOK_4_TITLE);
            $this->cartManager->addProduct($product4);
            $this->assertTrue($this->cartManager->hasProduct($product4));
        }
    }

    public function testCanAddProduct()
    {
        $authenticationData = $this->getAuthenticationData();

        foreach ($authenticationData as $data) {
            /** @var TokenInterface $token */
            $token = $data['token'];

            $this->initCart($token);

            $product1 = $this->getProduct(CampaignBookFixture::BOOK_1_TITLE);
            $this->assertTrue($this->cartManager->canAddProduct($product1));

            $this->cartManager->addProduct($product1);

            $this->assertFalse($this->cartManager->canAddProduct($product1));

            $product3 = $this->getProduct(CampaignBookFixture::BOOK_3_TITLE);
            $this->assertTrue($this->cartManager->canAddProduct($product3));

            $this->cartManager->addProduct($product3);

            $this->assertFalse($this->cartManager->canAddProduct($product1));
            $this->assertFalse($this->cartManager->canAddProduct($product3));

            // Unavailable status
            $product2 = $this->getProduct(CampaignBookFixture::BOOK_2_TITLE);
            $this->assertFalse($this->cartManager->canAddProduct($product2));

            // Another campaign
            $product4 = $this->getProduct(CampaignBookFixture::BOOK_4_TITLE);
            $this->assertFalse($this->cartManager->canAddProduct($product4));

            $this->cartManager->removeProduct($product3);
            // Still another campaign
            $this->assertFalse($this->cartManager->canAddProduct($product4));

            $this->cartManager->removeProduct($product1);
            // Now cart is empty so books from another campaign can be added
            $this->assertTrue($this->cartManager->canAddProduct($product4));
        }
    }

    public function testGetSummary()
    {
        $authenticationData = $this->getAuthenticationData();

        foreach ($authenticationData as $data) {
            /** @var TokenInterface $token */
            $token = $data['token'];

            $this->initCart($token);

            $summary = $this->cartManager->getSummary();
            $this->assertEquals(
                [
                    'itemsCount' => 0,
                    'subtotal' => 0,
                    'shipping' => 0,
                    'tax' => 0,
                    'fmtFee' => 0,
                    'transactionFee' => 0,
                    'total' => 0,
                ],
                $summary->getArray()
            );

            $product1 = $this->getProduct(CampaignBookFixture::BOOK_1_TITLE);
            $this->assertFalse($this->cartManager->hasProduct($product1));
            $this->assertTrue($this->cartManager->canAddProduct($product1));

            $cartSummaryValues = [
                'subTotal' => $product1->getPrice(),
                'shippingTotal' => 1000,
                'taxTotal' => 500,
            ];
            $this->mockNebookService($cartSummaryValues);

            $this->cartManager->addProduct($product1);

            $this->assertTrue($this->cartManager->hasProduct($product1));
            $this->assertFalse($this->cartManager->canAddProduct($product1));

            $summary = $this->cartManager->getSummary();
            $expectedSummary = [
                    'itemsCount' => 1,
                    'subtotal' => $cartSummaryValues['subTotal'],
                    'shipping' => $cartSummaryValues['shippingTotal'],
                    'tax' => $cartSummaryValues['taxTotal'],
                ] + self::calculatePartOfCartSummary(
                    $cartSummaryValues['subTotal']
                    + $cartSummaryValues['shippingTotal']
                    + $cartSummaryValues['taxTotal']
                );

            $this->assertEquals($expectedSummary, $summary->getArray());

            $product3 = $this->getProduct(CampaignBookFixture::BOOK_3_TITLE);
            $this->assertFalse($this->cartManager->hasProduct($product3));
            $this->assertTrue($this->cartManager->canAddProduct($product3));

            $cartSummaryValues = [
                'subTotal' => $product1->getPrice() + $product3->getPrice(),
                'shippingTotal' => 1500,
                'taxTotal' => 1000,
            ];

            $this->mockNebookService($cartSummaryValues);

            $this->cartManager->addProduct($product3);

            $this->assertTrue($this->cartManager->hasProduct($product1));
            $this->assertFalse($this->cartManager->canAddProduct($product1));
            $this->assertTrue($this->cartManager->hasProduct($product3));
            $this->assertFalse($this->cartManager->canAddProduct($product3));

            $summary = $this->cartManager->getSummary();

            $expectedSummary = [
                    'itemsCount' => 2,
                    'subtotal' => $cartSummaryValues['subTotal'],
                    'shipping' => $cartSummaryValues['shippingTotal'],
                    'tax' => $cartSummaryValues['taxTotal'],
                ] + self::calculatePartOfCartSummary(
                    $cartSummaryValues['subTotal']
                    + $cartSummaryValues['shippingTotal']
                    + $cartSummaryValues['taxTotal']
                );

            $this->assertEquals($expectedSummary, $summary->getArray());
        }
    }

    public function testSave()
    {
        $authenticationData = $this->getAuthenticationData();

        $orderRepo = $this->em->getRepository(Order::class);

        foreach ($authenticationData as $data) {
            /** @var TokenInterface $token */
            $token = $data['token'];

            $initialCount = $orderRepo->total();

            $this->initCart($token);

            $this->assertEquals($initialCount, $orderRepo->total());

            $product1 = $this->getProduct(CampaignBookFixture::BOOK_1_TITLE);
            $this->cartManager->addProduct($product1);
            $this->assertEquals($initialCount, $orderRepo->total());

            $this->cartManager->save();
            $this->assertEquals($initialCount + 1, $orderRepo->total());

            $this->cartManager->delete();
            $this->assertEquals($initialCount, $orderRepo->total());
        }
    }

    public function testEstimate()
    {
        $authenticationData = $this->getAuthenticationData();

        foreach ($authenticationData as $data) {
            /** @var TokenInterface $token */
            $token = $data['token'];

            $this->initCart($token);

            $product1 = $this->getProduct(CampaignBookFixture::BOOK_1_TITLE);
            $cartSummaryValues = [
                'subTotal' => $product1->getPrice(),
                'shippingTotal' => 1000,
                'taxTotal' => 500,
            ];
            $this->mockNebookService($cartSummaryValues);

            $this->cartManager->addProduct($product1);

            $product3 = $this->getProduct(CampaignBookFixture::BOOK_3_TITLE);
            $cartSummaryValues = [
                'subTotal' => $product1->getPrice() + $product3->getPrice(),
                'shippingTotal' => 1500,
                'taxTotal' => 1000,
            ];
            $this->mockNebookService($cartSummaryValues);

            $this->cartManager->addProduct($product3);

            $summary = $this->cartManager->getSummary();
            $expectedSummary = [
                    'itemsCount' => 2,
                    'subtotal' => $cartSummaryValues['subTotal'],
                    'shipping' => $cartSummaryValues['shippingTotal'],
                    'tax' => $cartSummaryValues['taxTotal'],
                ] + self::calculatePartOfCartSummary(
                    $cartSummaryValues['subTotal']
                    + $cartSummaryValues['shippingTotal']
                    + $cartSummaryValues['taxTotal']
                );

            $this->assertEquals($expectedSummary, $summary->getArray());

            $estimateSummary = $this->cartManager->estimate([
                $product1,
                $product3,
            ]);

            $this->assertEquals($expectedSummary, $estimateSummary->getArray());
        }
    }

    #region Internal
    private function getAuthenticationData()
    {
        $anonToken = self::getAuthToken('anonymous');
        $donorToken = self::getAuthToken('donor');
        $studentToken = self::getAuthToken('student');

        return [
            [
                'token' => $anonToken,
                'expected' => [
                    'user' => null,
                    'anonymousTokenNotEmpty' => true,
                ]
            ],
            [
                'token' => $donorToken,
                'expected' => [
                    'user' => $donorToken->getUser(),
                    'anonymousTokenNotEmpty' => false,
                ]
            ],
            [
                'token' => $studentToken,
                'expected' => [
                    'user' => $studentToken->getUser(),
                    'anonymousTokenNotEmpty' => false,
                ]
            ],
        ];
    }

    private function initCart(TokenInterface $token)
    {
        $this->tokenStorage->setToken($token);
        $this->cartManager->initCart();
    }

    /**
     * @param string $userType
     * @return TokenInterface
     */
    private function getAuthToken(string $userType)
    {
        if (is_null(self::$tokens)) {
            $userRepo = $this->em->getRepository(User::class);
            /** @var User $donor */
            $donor = $userRepo->findOneBy(['username' => UserFixture::DONOR_USERNAME]);
            /** @var User $student */
            $student = $userRepo->findOneBy(['username' => UserFixture::STUDENT_USERNAME]);

            self::$tokens = [
                'anonymous' => new AnonymousToken('test', 'anon.'),
                'donor' => new UsernamePasswordToken($donor, null, 'main', $donor->getRoles()),
                'student' => new RememberMeToken($student, 'main', 'student_secret'),
            ];
        }

        return self::$tokens[$userType];
    }

    /**
     * @param string $title
     * @return CampaignBook
     * @throws \Exception
     */
    private function getProduct(string $title)
    {
        if (is_null(self::$books)) {
            $bookRepo = $this->em->getRepository(CampaignBook::class);
            $books = $bookRepo->findAll();

            foreach ($books as $book) {
                self::$books[$book->getTitle()] = $book;
            }
        }

        if (empty(self::$books[$title])) {
            throw new \Exception(sprintf('No book with title "%s"', $title));
        }

        return self::$books[$title];
    }

    private function mockNebookService(array $cartSummaryValues = [])
    {
        $nebookProcessor = $this->container->get('test.domain.service.cart.processor.nebook');

        $result = new CartSummary();
        $resultWrapper = new DataHelper($result);

        foreach ($cartSummaryValues as $property => $value) {
            $resultWrapper->$property = $value;
        }

        $nebookServiceStub = $this->createMock(NebookService::class);
        $nebookServiceStub->method('getOrderSummary')->willReturn($result);

        $reflectionClass = new \ReflectionClass(NebookProcessor::class);
        $reflectionProperty = $reflectionClass->getProperty('nebookService');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($nebookProcessor, $nebookServiceStub);
        $reflectionProperty->setAccessible(false);
    }

    /**
     * @param int $subtotal
     * @return array
     */
    private static function calculatePartOfCartSummary(int $subtotal)
    {
        $fmtFee = intval(ceil($subtotal * 0.05));

        $static = 30;
        $commission = 0.029;

        $charge = intval(ceil(($subtotal + $fmtFee + $static) / (1 - $commission)));
        $transactionFee = intval(ceil($charge * $commission)) + $static;

        $total = $subtotal + $fmtFee + $transactionFee;

        return [
            'fmtFee' => $fmtFee,
            'transactionFee' => $transactionFee,
            'total' => $total,
        ];
    }

    protected function getFixtures(): array
    {
        return [
            UserFixture::class,
            CampaignFixture::class,
            CampaignBookFixture::class,
        ];
    }
    #endregion
}
