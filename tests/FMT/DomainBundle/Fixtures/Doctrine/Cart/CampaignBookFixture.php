<?php

namespace Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignBook;
use \DateTime;

/**
 * Class CampaignBookFixture
 * @package Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart
 */
class CampaignBookFixture extends Fixture
{
    const BOOK_1_TITLE = 'book 1';
    const BOOK_2_TITLE = 'book 2';
    const BOOK_3_TITLE = 'book 3';
    const BOOK_4_TITLE = 'book 4';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getCampaignBooks() as $data) {
            $campaignBook = new CampaignBook();
            /** @var Campaign $campaign */
            $campaign = $this->getReference($data['campaign_reference']);
            $campaignBook->setCampaign($campaign);
            $campaignBook->setProductFamilyId($data['campaign_reference']);
            $campaignBook->setTitle($data['title']);
            $campaignBook->setAuthor($data['author']);
            $campaignBook->setClass($data['class']);
            $campaignBook->setIsbn($data['isbn']);
            $campaignBook->setPrice($data['price']);
            $campaignBook->setQuantity($data['quantity']);
            $campaignBook->setStatus($data['status']);
            $campaignBook->setState($data['state']);
            $campaignBook->setCreatedAt($data['created_at']);
            $campaignBook->setUpdatedAt($data['updated_at']);
            $campaignBook->setSku($data['sku']);

            $manager->persist($campaignBook);

            $this->setReference($data['reference'], $campaignBook);
        }

        $manager->flush();
    }

    private function getCampaignBooks()
    {
        return [
            [
                'reference' => 'campaign_book_1',
                'campaign_reference' => 'test_campaign_1',
                'product_family_id' => '1111111',
                'title' => self::BOOK_1_TITLE,
                'author' => 'author 1',
                'class' => 'class 1',
                'isbn' => '111111111111',
                'price' => '1000',
                'quantity' => '1',
                'status' => CampaignBook::STATUS_AVAILABLE,
                'state' => CampaignBook::STATE_NEW,
                'created_at' => new DateTime('2016-01-01'),
                'updated_at' => new DateTime('2016-01-01'),
                'sku' => '111111111',
            ],
            [
                'reference' => 'campaign_book_2',
                'campaign_reference' => 'test_campaign_1',
                'product_family_id' => '1111111',
                'title' => self::BOOK_2_TITLE,
                'author' => 'author 2',
                'class' => 'class 1',
                'isbn' => '222222222222',
                'price' => '5000',
                'quantity' => '1',
                'status' => CampaignBook::STATUS_OUT_OF_STOCK,
                'state' => CampaignBook::STATE_UNKNOWN,
                'created_at' => new DateTime('2016-02-01'),
                'updated_at' => new DateTime('2016-02-01'),
                'sku' => '2222222222',
            ],
            [
                'reference' => 'campaign_book_3',
                'campaign_reference' => 'test_campaign_1',
                'product_family_id' => '2222222',
                'title' => self::BOOK_3_TITLE,
                'author' => 'author 3',
                'class' => 'class 2',
                'isbn' => '333333333333',
                'price' => '10000',
                'quantity' => '1',
                'status' => CampaignBook::STATUS_AVAILABLE,
                'state' => CampaignBook::STATE_USED,
                'created_at' => new DateTime('2016-03-01'),
                'updated_at' => new DateTime('2016-04-01'),
                'sku' => '33333333333',
            ],
            [
                'reference' => 'campaign_book_4',
                'campaign_reference' => 'test_campaign_2',
                'product_family_id' => '33333333',
                'title' => self::BOOK_4_TITLE,
                'author' => 'author 4',
                'class' => 'class 2',
                'isbn' => '444444444444',
                'price' => '500',
                'quantity' => '1',
                'status' => CampaignBook::STATUS_AVAILABLE,
                'state' => CampaignBook::STATE_NEW,
                'created_at' => new DateTime('2016-01-01'),
                'updated_at' => new DateTime('2016-01-01'),
                'sku' => '44444444444',
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            CampaignFixture::class,
        ];
    }
}
