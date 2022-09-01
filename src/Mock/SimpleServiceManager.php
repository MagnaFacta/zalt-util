<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.9.2
 */
class SimpleServiceManager implements ContainerInterface
{
    /**
     * @param array $objects Array with already created objects (it is simple)
     */
    public function __construct(protected array $objects = [])
    { }
        
    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        throw new ContainerNotFoundException("id $id not found!");
    }

    /**
     * @inheritDoc
     */
    public function has(string $id) : bool
    {
        return (bool) isset($this->objects[$id]);
    }
    
    public function set(string $id, mixed $obj): void
    {
        $this->objects[$id] = $obj;
    }
}