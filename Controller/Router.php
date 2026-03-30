<?php
namespace Swissup\Testimonials\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\UrlInterface;
use Swissup\Testimonials\Helper\Config;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ActionFactory $actionFactory
     * @param Config $config
     */
    public function __construct(
        ActionFactory $actionFactory,
        Config $config
    ) {
        $this->actionFactory = $actionFactory;
        $this->config = $config;
    }

    /**
     * Match a configured URL slug and forward to the internal testimonials route.
     *
     * Handles:
     *   /{slug}              → testimonials/index/index
     *   /{slug}/index        → testimonials/index/index
     *   /{slug}/index/index  → testimonials/index/index
     *   /{slug}/new          → testimonials/index/new
     *   /{slug}/index/new    → testimonials/index/new
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request): ?ActionInterface
    {
        $slug = trim($this->config->getUrlPath(), '/');
        if ($slug === '' || $slug === 'testimonials') {
            // Default slug — the standard Magento router handles it; nothing to do.
            return null;
        }

        $currentPath = trim($request->getPathInfo(), '/');

        $targetPath = null;
        if (in_array($currentPath, [$slug, $slug . '/index', $slug . '/index/index'], true)) {
            $targetPath = '/testimonials/index/index';
        } elseif (in_array($currentPath, [$slug . '/new', $slug . '/index/new'], true)) {
            $targetPath = '/testimonials/index/new';
        }

        if ($targetPath === null) {
            return null;
        }

        $request->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $currentPath);
        $request->setPathInfo($targetPath);

        return $this->actionFactory->create(Forward::class);
    }
}
