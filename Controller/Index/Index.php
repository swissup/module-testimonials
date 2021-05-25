<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swissup\Testimonials\Helper\Config $configHelper
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $layout = $this->configHelper->getListLayout();
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setPageLayout($layout);

        $pageConfig->addRemotePageAsset(
            $this->_url->getUrl('testimonials'),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return $resultPage;
    }
}
