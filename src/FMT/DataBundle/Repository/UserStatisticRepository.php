<?php

namespace FMT\DataBundle\Repository;

use FMT\DataBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Repository\UserStatisticRepositoryInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class UserStatisticRepository
 * @package FMT\DataBundle\Repository
 */
class UserStatisticRepository extends DoctrineRepository implements UserStatisticRepositoryInterface
{
    const UPDATE_STUDENTS_FOUNDED_QUERY = 'UPDATE user_statistic us SET
        us.students_founded = (
            SELECT COUNT(1) as `count` 
            FROM (
                SELECT 1 
                FROM user_transaction ut 
                WHERE ut.sender_id = :userId 
                GROUP BY ut.recipient_id
                ) sq     
        )
        WHERE us.id IN (SELECT statistic_id FROM user WHERE id = :userId)';

    const UPDATE_AMOUNT_FOUNDED_QUERY = 'UPDATE user_statistic us SET
        us.amount_founded = (
            SELECT SUM(ut.net) FROM user_transaction ut
            WHERE ut.sender_id = :userId
        )
        WHERE us.id IN (SELECT statistic_id FROM user WHERE id = :userId)';

    const UPDATE_AMOUNT_DONATED_TO_QUERY = 'UPDATE user_statistic us SET
        us.donated_to_me = (
            SELECT
                    IFNULL(
                       (SELECT SUM(ut.net) FROM user_transaction ut WHERE ut.recipient_id = :userId AND ut.type = :type)
                       , 0
                    )
                    +
                    IFNULL(
                        (SELECT sum(order.price) FROM user_transaction ut 
                            JOIN `order` ON ut.order_id = `order`.id 
                            WHERE ut.recipient_id = :userId AND ut.type = :type_for_order)
                        , 0
                    )
        )
        WHERE us.id = (SELECT statistic_id FROM user WHERE id = :userId)';

    /**
     * @param User $user
     * @throws DBALException
     */
    public function updateStudentsFounded(User $user)
    {
        $this->executeUpdateStatisticQuery(self::UPDATE_STUDENTS_FOUNDED_QUERY, [
            'userId' => $user->getId(),
        ]);
    }

    /**
     * @param User $user
     * @SuppressWarnings(PHPCS.UnusedFormalParameter)
     */
    public function updateBooksPurchasedFor(User $user)
    {
        // todo implement when book purchasing will be implemented
    }

    /**
     * @param User $user
     * @throws DBALException
     */
    public function updateAmountFounded(User $user)
    {
        $this->executeUpdateStatisticQuery(self::UPDATE_AMOUNT_FOUNDED_QUERY, [
            'userId' => $user->getId(),
        ]);
    }

    /**
     * @param UserInterface $user
     * @throws DBALException
     */
    public function updateAmountDonatedTo(UserInterface $user)
    {
        $this->executeUpdateStatisticQuery(self::UPDATE_AMOUNT_DONATED_TO_QUERY, [
            'userId' => $user->getId(),
            'type' => UserTransaction::TXN_DONATION,
            'type_for_order' => UserTransaction::TXN_BOOK_PURCHASE,
        ]);
    }

    /**
     * @param string $query
     * @param array $params
     * @throws DBALException
     */
    private function executeUpdateStatisticQuery(string $query, array $params = [])
    {
        $em = $this->getEntityManager();
        $em->getConnection()->executeUpdate($query, $params);
    }
}
