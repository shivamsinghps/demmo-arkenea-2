<?php

namespace FMT\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_statistic")
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\UserStatisticRepository")
 */
class UserStatistic
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", mappedBy="statistic")
     */
    private $user;

    /**
     * @var integer
     * @ORM\Column(name="students_founded", type="integer", nullable=false, options={"default" : 0})
     */
    private $studentsFounded = 0;

    /**
     * @var integer
     * @ORM\Column(name="books_purchased_for", type="integer", nullable=false, options={"default" : 0})
     */
    private $booksPurchasedFor = 0;

    /**
     * @var integer
     * @ORM\Column(name="amount_founded", type="integer", nullable=false, options={"default" : 0})
     */
    private $amountFounded = 0;

    /**
     * @var integer
     * @ORM\Column(name="donated_to_me", type="integer", nullable=false, options={"default": 0})
     */
    private $donatedToMe = 0;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return UserStatistic
     */
    public function setUser(User $user): UserStatistic
    {
        $this->user = $user;
        if ($user->getStatistic() !== $this) {
            $user->setStatistic($this);
        }

        return $this;
    }


    /**
     * @return integer
     */
    public function getStudentsFounded(): int
    {
        return $this->studentsFounded;
    }

    /**
     * @param integer $studentsFounded
     * @return UserStatistic
     */
    public function setStudentsFounded(int $studentsFounded): self
    {
        $this->studentsFounded = $studentsFounded;

        return $this;
    }

    /**
     * @return integer
     */
    public function getBooksPurchasedFor(): int
    {
        return $this->booksPurchasedFor;
    }

    /**
     * @param integer $booksPurchasedFor
     * @return UserStatistic
     */
    public function setBooksPurchasedFor(int $booksPurchasedFor): self
    {
        $this->booksPurchasedFor = $booksPurchasedFor;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAmountFounded(): int
    {
        return $this->amountFounded;
    }

    /**
     * @param integer $amountFounded
     * @return UserStatistic
     */
    public function setAmountFounded(int $amountFounded): self
    {
        $this->amountFounded = $amountFounded;

        return $this;
    }

    /**
     *  todo Add real count of books purchased after book purchasing implementation
     * @return int
     */
    public function getBooksPurchasedMe()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getDonatedToMe(): int
    {
        return $this->donatedToMe;
    }

    /**
     * @param int $donatedToMe
     * @return $this
     */
    public function setDonatedToMe(int $donatedToMe)
    {
        $this->donatedToMe = $donatedToMe;

        return $this;
    }
}
