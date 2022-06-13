<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 14:09
 */

namespace FMT\InfrastructureBundle\Service\Mapper;

/**
 * Class AbstractMapper
 * @package FMT\InfrastructureBundle\Service\Mapper
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractMapper
{
    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * ATTENTION: The class that extends current abstract class should also implement following method:
     *   public function map(Type $source) : Type
     *
     * Unfortunately, PHP 7.0 does not support generics, so declaration of abstract method here will cause an error on
     * method override in child class.
     */

    /**
     * Method transforms float value of price into integer value
     *
     * @param float $value
     * @return int
     */
    protected function toIntPrice($value)
    {
        if (is_null($value)) {
            return null;
        }

        $parts = explode(".", number_format($value, 2, ".", ""), 2);
        return 100 * (int) $parts[0] + (int) $parts[1];
    }

    /**
     * Method transforms integer value of price into float
     *
     * @param int $value
     * @return float
     */
    protected function fromIntPrice($value)
    {
        if (is_null($value)) {
            return null;
        }

        $int = "0";
        $decimal = sprintf("%02d", $value);
        if (strlen($decimal) > 2) {
            $int = substr($decimal, 0, -2);
            $decimal = substr($decimal, -2);
        }

        return floatval(sprintf("%s.%s", $int, $decimal));
    }

    /**
     * Method maps name-value-pair to PHP dictionary
     *
     * @param array $attributes
     * @return array
     */
    protected function mapNvpToDict($attributes)
    {
        $result = [];
        usort($attributes, [$this, "sortAttributes"]);
        foreach ($attributes as $nvp) {
            if (!isset($nvp["Name"])) {
                continue;
            }

            $result[$nvp["Name"]] = isset($nvp["Value"]) ? (string) $nvp["Value"] : null;
        }

        return $result;
    }

    /**
     * Method compares two element of array by sort order attribute. It could be used to sort NVP list.
     *
     * @param array $attr1
     * @param array $attr2
     * @return int
     */
    protected function sortAttributes($attr1, $attr2)
    {
        $order1 = isset($attr1["SortOrder"]) ? (int) $attr1["SortOrder"] : null;
        $order2 = isset($attr2["SortOrder"]) ? (int) $attr2["SortOrder"] : null;

        if ($order1 == $order2) {
            return 0;
        }
        return ($order1 < $order2) ? -1 : 1;
    }
}
