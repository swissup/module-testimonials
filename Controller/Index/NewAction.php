<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class NewAction implements HttpGetActionInterface
{
    /**
     * @var \Swissup\Testimonials\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ResultFactory $resultFactory
     * @param ResponseInterface $response
     */
    public function __construct(
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        ResultFactory $resultFactory,
        ResponseInterface $response
    ) {
        $this->configHelper = $configHelper;
        $this->customerSession = $customerSession;
        $this->resultFactory = $resultFactory;
        $this->response = $response;
    }

    /**
     * @return \Magento\Framework\View\Result\Page|ResponseInterface
     */
    public function execute()
    {
        if (!$this->configHelper->guestSubmitAllowed() &&
            !$this->customerSession->authenticate()
        ) {
            // authenticate() redirects the customer to the login page
            // and returns false; return the response to complete the redirect.
            return $this->response;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setPageLayout($this->configHelper->getFormLayout());

        return $resultPage;
    }
}
