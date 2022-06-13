<?php

namespace Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\User;
use \DateTime;

/**
 * Class CampaignFixture
 * @package Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart
 */
class CampaignFixture extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getCampaigns() as $data) {
            $campaign = new Campaign();
            /** @var User $user */
            $user = $this->getReference($data['user_reference']);
            $campaign->setUser($user);
            $campaign->setStartDate($data['start_date']);
            $campaign->setEndDate($data['end_date']);
            $campaign->setShippingOption($data['shipping_option']);

            $manager->persist($campaign);

            $this->setReference($data['reference'], $campaign);
        }

        $manager->flush();
    }

    private function getCampaigns()
    {
        return [
            [
                'reference' => 'test_campaign_1',
                'user_reference' => 'student',
                'shipping_option' => $this->container->getParameter('nebook_default_shipping_id'),
                'start_date' => new DateTime('2018-01-01'),
                'end_date' => new DateTime('2018-03-01'),
            ],
            [
                'reference' => 'test_campaign_2',
                'user_reference' => 'student',
                'shipping_option' => $this->container->getParameter('nebook_default_shipping_id'),
                'start_date' => new DateTime('2018-04-01'),
                'end_date' => new DateTime('2018-10-01'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
        ];
    }
}
