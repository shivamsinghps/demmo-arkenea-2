<?php

namespace Tests\FMT\DomainBundle\Service\Cart;

use Doctrine\ORM\EntityManagerInterface;
use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\Cart\Provider\AnonymousProvider;
use FMT\DomainBundle\Service\CartManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\FMT\DomainBundle\AbstractDbTest;
use Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart\CampaignBookFixture;
use Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart\CampaignFixture;
use Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart\OrderFixture;
use Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart\UserFixture;

/**
 * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help
 *
 * Class GetCartTest
 * @package Tests\FMT\DomainBundle\Service\Cart
 */
class GetCartTest extends AbstractDbTest
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

        $this->mockAnonymousProvider(OrderFixture::ORDER_ANON_TOKEN);
    }

    /**
     * @param string $userType
     * @param array $expected
     *
     * @dataProvider getAuth
     */
    public function testGetCart(string $userType, array $expected)
    {
        $token = self::getAuthToken($userType);

        $this->tokenStorage->setToken($token);
        $this->cartManager->initCart();

        $cart = $this->cartManager->get();

        $this->assertEquals($expected['price'], $cart->getPrice());
        $this->assertEquals($expected['shipping'], $cart->getShipping());
        $this->assertEquals($expected['tax'], $cart->getTax());
        $this->assertEquals($expected['transactionFee'], $cart->getTransactionFee());
        $this->assertEquals($expected['fmtFee'], $cart->getFmtFee());
        $this->assertEquals($expected['total'], $cart->getTotal());
        $this->assertEquals($expected['itemsCount'], $cart->getItems()->count());
    }

    public function getAuth()
    {
        return [
            [
                'userType' => 'student',
                [
                    'price' => 6000,
                    'shipping' => 2000,
                    'tax' => 300,
                    'transactionFee' => 124,
                    'fmtFee' => 234,
                    'total' => 234234,
                    'itemsCount' => 2,
                ]
            ],
            [
                'userType' => 'donor',
                [
                    'price' => 345345,
                    'shipping' => 23452345,
                    'tax' => 23452345,
                    'transactionFee' => 2345234,
                    'fmtFee' => 32452345,
                    'total' => 32453245,
                    'itemsCount' => 0,
                ]
            ],
            [
                'userType' => 'anonymous',
                [
                    'price' => 546745,
                    'shipping' => 4567456,
                    'tax' => 4567,
                    'transactionFee' => 4567,
                    'fmtFee' => 4567,
                    'total' => 456745,
                    'itemsCount' => 0,
                ]
            ],
        ];
    }

    #region Internal

    /**
     * @param string $token
     */
    private function mockAnonymousProvider(string $token)
    {
        $anonymousProvider = $this->container->get('test.domain.service.cart.provider.anonymous');

        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('get')->will(
            $this->returnCallback(
                function ($key) use ($token) {
                    return $key === AnonymousProvider::CART_TOKEN_KEY ? $token : null;
                }
            )
        );

        $anonymousProviderReflectionClass = new \ReflectionClass(AnonymousProvider::class);
        $reflectionProperty = $anonymousProviderReflectionClass->getProperty('session');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($anonymousProvider, $sessionMock);
        $reflectionProperty->setAccessible(false);
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

    protected function getFixtures(): array
    {
        return [
            UserFixture::class,
            CampaignFixture::class,
            CampaignBookFixture::class,
            OrderFixture::class,
        ];
    }
    #endregion
}
