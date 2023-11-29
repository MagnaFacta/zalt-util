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
class RequestInfo
{
    protected $params = [];
    
    public function __construct(
        protected readonly ?string $currentController,
        protected readonly ?string $currentAction,
        protected readonly ?string $routeName,
        protected readonly ?string $basePath = '',
        protected readonly bool $isPost = false,
        protected readonly array $requestMatchedParams = [],
        protected readonly array $requestPost = [],
        protected readonly array $requestQueryParams = []
    ) {
        $this->params = $this->requestMatchedParams + $this->requestPost + $this->requestQueryParams;
    }
    
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    /**
     * Get the current action name
     *
     * @return string|null
     */
    public function getCurrentAction(): ?string
    {
        return $this->currentAction;
    }

    /**
     * Get the current Controller name
     *
     * @return string|null
     */
    public function getCurrentController(): ?string
    {
        return $this->currentController;
    }

    /**
     * @return string|null Returns the current url minus all the string parameters
     */
    public function getCurrentPage(): ?string
    {
        $page = $this->getBasePath();
        if ($page) {
            foreach ($this->getRequestMatchedParams() as $value) {
                if (str_contains($page, '/' . $value)) {
                    $page = substr($page, 0, strpos($page, '/' . $value));
                }
            }
        }

        return $page;
    }

    public function getParam(string $name, mixed $default = null): mixed
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        return $default;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getRequestMatchedParams(): array
    {
        return $this->requestMatchedParams;
    }

    /**
     * @return array POST request content
     */
    public function getRequestPostParams(): array
    {
        return $this->requestPost;
    }

    /**
     * @return array query params
     */
    public function getRequestQueryParams(): array
    {
        return $this->requestQueryParams;
    }

    /**
     * @return string|null
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }
    
    /**
     * @return bool is the current request a POST request?
     */
    public function isPost(): bool
    {
        return $this->isPost;
    }
}