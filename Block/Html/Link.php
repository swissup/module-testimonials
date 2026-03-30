<?php
namespace Swissup\Testimonials\Block\Html;

use Swissup\Testimonials\Helper\Config;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    /**
     * Return the testimonials list URL using the configured slug.
     *
     * Uses _direct to avoid the trailing slash that getUrl($routePath) appends
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('', ['_direct' => $this->configHelper->getUrlPath()]);
    }
}
