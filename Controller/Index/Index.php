<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Swissup\Testimonials\Helper\Config;

class Index implements HttpGetActionInterface
{
    /**
     * @var Config
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
     * @param Config $configHelper
     * @param ResultFactory $resultFactory
     * @param UrlInterface $url
     */
    public function __construct(
        Config $configHelper,
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
            $this->url->getUrl('', ['_direct' => $this->configHelper->getUrlPath()]),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return $resultPage;
    }
}
