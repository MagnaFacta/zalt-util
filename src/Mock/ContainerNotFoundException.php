<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use Psr\Container\NotFoundExceptionInterface;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class ContainerNotFoundException extends \Exception implements NotFoundExceptionInterface
{ }