<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;

class Index implements HttpGetActionInterface
{
    /**
     * @var \Swissup\Testimonials\Helper\Config
     */
    private $configHelper;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param ResultFactory $resultFactory
     * @param UrlInterface $url
     */
    public function __construct(
        \Swissup\Testimonials\Helper\Config $configHelper,
        ResultFactory $resultFactory,
        UrlInterface $url
    ) {
        $this->configHelper = $configHelper;
        $this->resultFactory = $resultFactory;
        $this->url = $url;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setPageLayout($this->configHelper->getListLayout());

        $pageConfig->addRemotePageAsset(
            $this->url->getUrl('testimonials'),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return $resultPage;
    }
}
