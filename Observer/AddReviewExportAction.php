<?php
namespace Swissup\Testimonials\Observer;

class AddReviewExportAction implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Add copy to testimonials in product review mass actions
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof \Magento\Review\Block\Adminhtml\Grid &&
            $block->getRequest()->getFullActionName() == 'review_product_index'
        ) {
            $block->getMassactionBlock()->addItem(
                'copy_to_testimonials',
                [
                    'label' => __('Copy to Testimonials'),
                    'url' => $block->getUrl(
                        'testimonials/index/massSaveReview',
                        ['_current' => true]
                    )
                ]
            );
        }
    }
}
