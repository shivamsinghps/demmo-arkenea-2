<?php
/**
 * Author: Anton Orlov
 * Date: 27.02.2018
 * Time: 14:40
 */

namespace FMT\InfrastructureBundle\Service\Nebook;

/**
 * Options
 */
class Options
{
    /** @var string */
    public $endpoint;

    /** @var string */
    public $wsdl;

    /** @var string */
    public $xmlns;

    /** @var string */
    public $bookstore_id;

    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @var int */
    public $timeout;

    public function __construct(array $options)
    {
        $this->endpoint = $options['endpoint'];
        $this->wsdl = $options['wsdl'];
        $this->xmlns = $options['xmlns'];
        $this->bookstore_id = $options['bookstore_id'];
        $this->username = $options['username'];
        $this->password = $options['password'];
        $this->timeout = $options['timeout'];
    }
}
