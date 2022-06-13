<?php

namespace Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FMT\DataBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixture
 * @package Tests\FMT\DomainBundle\Fixtures\Doctrine\Cart
 */
class UserFixture extends Fixture
{
    const DONOR_USERNAME = 'test.donor@example.com';
    const STUDENT_USERNAME = 'test.student@example.com';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getUsers() as $data) {
            $user = new User();
            $user->setRoles($data['roles']);
            $user->setUsername($data['username']);
            $user->getProfile()->setEmail($data['username']);
            $password = $data['password'];
            $user->setPassword($password);


            $manager->persist($user);

            $this->setReference($data['reference'], $user);
        }

        $manager->flush();
    }

    private function getUsers()
    {
        return [
            [
                'reference' => 'student',
                'roles' => [User::ROLE_STUDENT],
                'username' => 'test.student@example.com',
                'password' => 'test_student_password_A1!'
            ],
            [
                'reference' => 'donor',
                'roles' => [User::ROLE_DONOR],
                'username' => 'test.donor@example.com',
                'password' => 'test_donor_password_A1!'
            ],
        ];
    }
}
