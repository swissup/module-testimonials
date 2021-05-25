<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Load extends \Magento\Framework\App\Action\Action
{
    /**
     * Layout Factory
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $currentPage = (int)$this->getRequest()->getParam('page', 1);
        $testimonialsListBlock = $this->layoutFactory->create()
            ->createBlock(\Swissup\Testimonials\Block\TestimonialsList::class)
            ->setTemplate('Swissup_Testimonials::list.phtml')
            ->setCurrentPage($currentPage)
            ->setIsAjax(true);

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData([
            'outputHtml' => $testimonialsListBlock->toHtml(),
            'lastPage' => $testimonialsListBlock->isLastPage()
        ]);
    }
}
