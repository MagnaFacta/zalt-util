<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use pathInfo;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BaseUrlFactory
{
    public function __invoke(ContainerInterface $container): BaseUrl
    {
        $output = new BaseUrl();

        if ($container->has(ServerRequestInterface::class)) {
            $request = $container->get(ServerRequestInterface::class);
            
            if ($request instanceof ServerRequestInterface) {
                $path = $request->getUri()->getPath();
                if (pathInfo($path, PATHINFO_EXTENSION)) {
                    $output->setBaseUrl(dirname($path));
                } else {
                    $output->setBaseUrl($path);
                }
            }
        }

        if (class_exists('Zalt\Html\Html')) {
            \Zalt\Html\Html::getRenderer()->setBaseUrl($output);
        }

        return $output;
    }
}