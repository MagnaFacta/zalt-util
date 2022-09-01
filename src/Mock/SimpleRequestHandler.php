<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class SimpleRequestHandler implements RequestHandlerInterface
{
    private static SimpleRequestHandler $_instance;
    
    private ?ServerRequestInterface $request;
    
    public static function getInstance()
    {
        if (! isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }
    
    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $this->request = $request;
        
        $output = new Response(
            200,
            $request->getHeaders(),
            $request->getBody()
        );
        
        return $output;
    }
}