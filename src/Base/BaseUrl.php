<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BaseUrl 
{
    /**
     * BaseUrl
     *
     * @var string
     */
    protected $_baseUrl = '';
    
    public function __toString(): string
    {
        return $this->_baseUrl;
    }

    public function getBaseUrl(): string
    {
        return $this->_baseUrl;
    }

    public function setBaseUrl(string $base): void
    {
        $this->_baseUrl = rtrim($base, '/\\');
    }
}
