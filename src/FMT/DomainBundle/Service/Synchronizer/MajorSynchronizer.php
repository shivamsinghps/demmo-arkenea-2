<?php

namespace FMT\DomainBundle\Service\Synchronizer;

use Doctrine\Common\Collections\ArrayCollection;
use FMT\DataBundle\Doctrine\Extensions\DBAL\Types\AbstractDateTimeType;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\UserProfile;
use FMT\DomainBundle\Repository\UserRepositoryInterface;
use FMT\DomainBundle\Service\SynchronizerInterface;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client as NebookClient;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Department;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Term;
use FMT\InfrastructureBundle\Helper\LogHelper;

/**
 * Class MajorSynchronizer
 * @package FMt\DomainBundle\Service\Synchronizer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MajorSynchronizer implements SynchronizerInterface
{
    const MAJOR_EXPIRED_TIMEOUT = 3600;

    /** @var  UserRepositoryInterface */
    private $repository;

    /** @var NebookClient */
    private $client;

    /** @var bool */
    private $isForActiveCampaign = false;

    /** @var array  */
    private $visibilityData = [UserProfile::VISIBILITY_ALL];

    /**
     * MajorSynchronizer constructor.
     * @param UserRepositoryInterface $repository
     * @param NebookClient $client
     */
    public function __construct(UserRepositoryInterface $repository, NebookClient $client)
    {
        $this->repository = $repository;
        $this->client = $client;
    }

    /**
     * @return UserMajor[]|ArrayCollection
     */
    public function synchronize()
    {
        // TODO: Cache major list using framework cache

        $result = null;
        try {
            if ($this->isSyncNeeded()) {
                $departments = [];
                $this->repository->beginTransaction();
                $majors = $this->repository->getMajors(true);

                /**
                 * @var $major UserMajor
                 */
                foreach ($majors as $major) {
                    $major->setActive(false);
                    $this->repository->save($major);
                }

                foreach ($this->getCampusIdentifiers() as $campusId) {
                    $departments[$campusId] = $this->client->departmentsGetByCampusId($campusId);
                }

                $this->saveMajor($departments, $majors);

                $this->repository->commit();

                $iterator = $majors->filter(function (UserMajor $item) {
                    return $item->isActive();
                })->getIterator();
                $iterator->uasort(function (UserMajor $itemA, UserMajor $itemB) {
                    return $itemA->getName() <=> $itemB->getName();
                });
                $result = new ArrayCollection(iterator_to_array($iterator));
            }
        } catch (\Exception  $e) {
            $this->repository->rollback();
            LogHelper::error($e);
        }

        if (is_null($result)) {
            $result = $this->repository->getMajors(false, $this->isForActiveCampaign, $this->visibilityData);
        }

        return $result;
    }

    /**
     * @param bool $forActiveCampaign
     * @return $this
     */
    public function setForActiveCampaign(bool $forActiveCampaign)
    {
        $this->isForActiveCampaign = $forActiveCampaign;

        return $this;
    }

    /**
     * @param array $visibilityData
     */
    public function setVisibilityData(array $visibilityData)
    {
        $this->visibilityData = $visibilityData;

        return $this;
    }

    /**
     * @return array
     */
    private function getCampusIdentifiers()
    {
        $openedTerms = $this->client->termsGetOpened();

        $campusesId = array_map(function (Term $item) {
            return $item->getCampus()->getId();
        }, $openedTerms);

        return array_unique($campusesId);
    }

    /**
     * @param $departments
     * @param ArrayCollection $majors
     */
    private function saveMajor($departments, ArrayCollection $majors)
    {
        foreach ($departments as $campusId => $departmentsArray) {
            if (is_array($departmentsArray)) {
                /**
                 * @var $department Department
                 */
                foreach ($departmentsArray as $department) {
                    $departmentId = $department->getId();

                    $userMajor = $majors->filter(function (UserMajor $major) use ($campusId, $departmentId) {
                        return $major->getCampusId() == $campusId && $major->getDepartmentId() == $departmentId;
                    })->first();

                    if (!$userMajor instanceof UserMajor) {
                        $userMajor = new UserMajor();
                        $majors->add($userMajor);
                    }
                    $userMajor->setActive(true);
                    $userMajor->setCampusId($campusId);
                    $userMajor->setDepartmentId($department->getId());
                    $userMajor->setName($department->getName());

                    $this->repository->save($userMajor);
                }
            }
        }
    }

    /**
     * @return bool
     */
    private function isSyncNeeded()
    {
        $maxSyncDate = $this->repository->getMajorSyncDate();

        if ($maxSyncDate instanceof \DateTime) {
            $timeZone = new \DateTimeZone(AbstractDateTimeType::DEFAULT_TIME_ZONE);
            $expireDate = new \DateTime(sprintf('- %s seconds', MajorSynchronizer::MAJOR_EXPIRED_TIMEOUT));
            $expireDate->setTimezone($timeZone);

            return $maxSyncDate <= $expireDate;
        }

        return true;
    }
}
