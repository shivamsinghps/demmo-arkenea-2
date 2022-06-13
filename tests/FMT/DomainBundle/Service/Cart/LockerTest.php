<?php

namespace Tests\FMT\DomainBundle\Service\Cart;

use FMT\DomainBundle\Service\ResourceLockerInterface;
use Tests\FMT\InfrastructureBundle\AbstractTest;

/**
 * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help
 *
 * Class LockerTest
 * @package Tests\FMT\DomainBundle\Service\Cart
 */
class LockerTest extends AbstractTest
{
    /**
     * @var ResourceLockerInterface
     */
    private $locker;

    public function setUp()
    {
        parent::setUp();

        $this->locker = $this->container->get('test.domain.service.cart.locker');
    }

    public function testLockCart()
    {
        $this->assertFalse($this->locker->isLocked());
        $this->assertTrue($this->locker->lock());
        $this->assertTrue($this->locker->isLocked());
        $this->locker->release();
        $this->assertFalse($this->locker->isLocked());

        $this->assertTrue($this->locker->lock());
        $this->assertTrue($this->locker->isLocked());
        $this->locker->release();
        $this->assertFalse($this->locker->isLocked());
    }

    /**
     *
     */
    public function testLockLockedCart()
    {
        $this->assertFalse($this->locker->isLocked());
        $this->assertTrue($this->locker->lock());
        $this->assertTrue($this->locker->isLocked());
        $this->assertFalse($this->locker->lock());
    }
}
