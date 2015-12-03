<?php
namespace Swissup\Testimonials\Controller\Index;

class NewAction extends \Magento\Framework\App\Action\Action
{
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    protected $_configHelper;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Swissup\Testimonials\Helper\Config $configHelper
    )
    {
        $this->_configHelper = $configHelper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create(false, ['isIsolated' => true]);
        $layout = $this->_configHelper->getFormLayout();
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setPageLayout($layout);

        return $resultPage;
    }
}