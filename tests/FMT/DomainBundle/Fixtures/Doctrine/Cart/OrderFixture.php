<?php

namespace Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignBook;
use \DateTime;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\OrderItem;
use FMT\DataBundle\Entity\User;

/**
 * Class CampaignBookFixture
 * @package Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart
 */
class OrderFixture extends Fixture
{
    const ORDER_ANON_TOKEN = 'qewrqwerqwerqwerqwerqwerqwerqwerqwerqwerqwer';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getOrders() as $data) {
            $order = new Order();
            /** @var Campaign $campaign */
            $campaign = $this->getReference($data['campaign_reference']);
            $order->setCampaign($campaign);

            if (!empty($data['user_reference'])) {
                /** @var User $user */
                $user = $this->getReference($data['user_reference']);
                $order->setUser($user);
            }

            $order->setPrice($data['price']);
            $order->setShipping($data['shipping']);
            $order->setTax($data['tax']);
            $order->setTransactionFee($data['transaction_fee']);
            $order->setFmtFee($data['fmt_fee']);
            $order->setTotal($data['total']);
            $order->setStatus($data['status']);
            $order->setAnonymousToken($data['anonymous_token']);
            $order->setCreatedAt($data['created_at']);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $orderItem = new OrderItem();
                    $orderItem->setOrder($order);
                    $orderItem->setStatus($item['status']);
                    $orderItem->setQuantity(1);

                    /** @var CampaignBook $book */
                    $book = $this->getReference($item['book_reference']);

                    $orderItem->setPrice($book->getPrice());
                    $orderItem->setSku($book->getSku());
                    $orderItem->setTitle($book->getTitle());

                    $order->addItem($orderItem);
                }
            }

            $manager->persist($order);

            $this->setReference($data['reference'], $order);
        }

        $manager->flush();
    }

    private function getOrders()
    {
        return [
            [
                'reference' => 'order_1',
                'user_reference' => 'student',
                'campaign_reference' => 'test_campaign_1',
                'price' => '6000',
                'shipping' => '2000',
                'tax' => '300',
                'transaction_fee' => '124',
                'fmt_fee' => '234',
                'total' => '234234',
                'status' => 'cart',
                'anonymous_token' => null,
                'created_at' => new DateTime('2018-05-07'),
                'items' => [
                    [
                        'book_reference' => 'campaign_book_1',
                        'status' => 'cart',
                    ],
                    [
                        'book_reference' => 'campaign_book_2',
                        'status' => 'cart',
                    ],
                ],
            ],
            [
                'reference' => 'order_2',
                'user_reference' => 'donor',
                'campaign_reference' => 'test_campaign_2',
                'price' => '345345',
                'shipping' => '23452345',
                'tax' => '23452345',
                'transaction_fee' => '2345234',
                'fmt_fee' => '32452345',
                'total' => '32453245',
                'status' => 'cart',
                'anonymous_token' => null,
                'created_at' => new DateTime('2018-05-07'),
            ],
            [
                'reference' => 'order_3',
                'user_reference' => null,
                'campaign_reference' => 'test_campaign_1',
                'price' => '546745',
                'shipping' => '4567456',
                'tax' => '4567',
                'transaction_fee' => '4567',
                'fmt_fee' => '4567',
                'total' => '456745',
                'status' => 'cart',
                'anonymous_token' => self::ORDER_ANON_TOKEN,
                'created_at' => new DateTime('2018-05-07'),
            ],
            [
                'reference' => 'order_3',
                'user_reference' => null,
                'campaign_reference' => 'test_campaign_1',
                'price' => '546745',
                'shipping' => '4567456',
                'tax' => '4567',
                'transaction_fee' => '4567',
                'fmt_fee' => '4567',
                'total' => '456745',
                'status' => 'cart',
                'anonymous_token' => 'qqqqqqqqqqqqqqqqqqqqqqqq',
                'created_at' => new DateTime('2018-05-07'),
            ],
            [
                'reference' => 'order_1_2',
                'user_reference' => 'student',
                'campaign_reference' => 'test_campaign_1',
                'price' => '4567456',
                'shipping' => '4567456',
                'tax' => '456745',
                'transaction_fee' => '45674567',
                'fmt_fee' => '456754',
                'total' => '45675467',
                'status' => 'cart',
                'anonymous_token' => null,
                'created_at' => new DateTime('2018-04-07'),
            ],
            [
                'reference' => 'order_1_3',
                'user_reference' => 'student',
                'campaign_reference' => 'test_campaign_1',
                'price' => '4567456',
                'shipping' => '4567456',
                'tax' => '456745',
                'transaction_fee' => '45674567',
                'fmt_fee' => '456754',
                'total' => '45675467',
                'status' => 'completed',
                'anonymous_token' => null,
                'created_at' => new DateTime('2018-03-07'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            CampaignBookFixture::class,
        ];
    }
}
