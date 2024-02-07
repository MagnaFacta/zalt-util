<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

/**
 * Mock object implementing the Zalt\Base\TranslatorInterface
 *
 * This class exists a) because we've used the name in the past and b) because is nicer to use.
 *
 * As to the reason for the name: https://en.wikipedia.org/wiki/Potemkin_village
 * 
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class PotemkinTranslator extends MockTranslator
{ }