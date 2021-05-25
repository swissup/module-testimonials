<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Framework\App\Action\Action
{
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->configHelper = $configHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$request->isDispatched()) {
            return parent::dispatch($request);
        }

        if (!$this->configHelper->guestSubmitAllowed() &&
            !$this->customerSession->authenticate()
        ) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $layout = $this->configHelper->getFormLayout();
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setPageLayout($layout);

        return $resultPage;
    }
}
