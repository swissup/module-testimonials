<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\BlockFactory;

class Load implements HttpGetActionInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param BlockFactory $blockFactory
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     */
    public function __construct(
        BlockFactory $blockFactory,
        ResultFactory $resultFactory,
        RequestInterface $request
    ) {
        $this->blockFactory = $blockFactory;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $currentPage = (int)$this->request->getParam('page', 1);

        /** @var \Swissup\Testimonials\Block\TestimonialsList $testimonialsListBlock */
        $testimonialsListBlock = $this->blockFactory->createBlock(
            \Swissup\Testimonials\Block\TestimonialsList::class,
            [
                'data' => [
                    'current_page' => $currentPage,
                    'is_ajax' => true,
                ],
            ]
        );
        $testimonialsListBlock->setTemplate('Swissup_Testimonials::list.phtml');

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData([
            'outputHtml' => $testimonialsListBlock->toHtml(),
            'lastPage' => $testimonialsListBlock->isLastPage()
        ]);
    }
}
