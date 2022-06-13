<?php

namespace FMT\DataBundle\Model;

use FMT\DataBundle\Entity\UserMajor;

/**
 * Class BaseFilterOptions
 * @package FMT\DataBundle\Model
 */
class BaseFilterOptions
{
    const SORT_DIRECTION = [
        'DESC' => 'fmt.form.filter.sort_desc',
        'ASC' => 'fmt.form.filter.sort_asc',
    ];

    const RECORDS_LIMIT = [
        '20' => 20,
        '50' => 50,
        '100' => 100,
        '500' => 500,
        '1000' => 1000,
        'all' => 'fmt.form.filter.all',
    ];

    const DEFAULT_ALL_RECORDS_PER_PAGE = 99999999;

    /**
     * @var UserMajor
     */
    protected $major;

    /**
     * @var array
     */
    protected $sortBy;

    /**
     * @var string
     */
    protected $sortDirection;

    /**
     * @var integer
     */
    protected $limit;

    /**
     * @var string
     */
    protected $search;

    /**
     * @return mixed
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @param $major
     * @return $this
     */
    public function setMajor($major)
    {
        $this->major = $major;

        return $this;
    }

    /**
     * @return array
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param $sortBy
     * @return $this
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = explode(',', $sortBy);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param $sortDirection
     * @return $this
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $search
     * @return $this
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return int
     */
    public function getFilterLimit()
    {
        $limits = self::RECORDS_LIMIT;

        if (is_numeric($this->getLimit()) && in_array($this->getLimit(), $limits)) {
            return $this->getLimit();
        }

        if (is_string($this->getLimit()) && $this->getLimit() == 'all') {
            return self::DEFAULT_ALL_RECORDS_PER_PAGE;
        }


        return (int)array_shift($limits);
    }
}
