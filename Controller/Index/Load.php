<?php
namespace Swissup\Testimonials\Controller\Index;

class Load extends \Magento\Framework\App\Action\Action
{
    /**
     * Layout Factory
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    /**
     * Json encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    )
    {
        $this->layoutFactory = $layoutFactory;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context);
    }

    public function execute()
    {
        $currentPage = (int)$this->getRequest()->getParam('page', 1);
        $testimonialsListBlockHtml = $this->layoutFactory->create()
            ->createBlock('Swissup\Testimonials\Block\TestimonialsList')
            ->setTemplate('Swissup_Testimonials::list.phtml')
            ->setCurrentPage($currentPage)
            ->setIsAjax(true)
            ->toHtml();

        $this->getResponse()->setBody(
            $this->jsonEncoder->encode(array('outputHtml' => $testimonialsListBlockHtml))
        );
    }
}