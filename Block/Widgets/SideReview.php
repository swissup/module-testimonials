<?php
namespace Swissup\Testimonials\Block\Widgets;

use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
/**
 * Class side review widget
 * @package Swissup\Testimonials\Block\Widgets
 */
class SideReview extends \Magento\Framework\View\Element\Template
     implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Default template to use for review widget
     */
    const DEFAULT_REVIEW_TEMPLATE = 'widgets/sidereview.phtml';

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_testimonialsCollectionFactory = $testimonialsCollectionFactory;
    }

    public function _construct()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_REVIEW_TEMPLATE);
        }
        return parent::_construct();
    }
    /**
     * @return \Swissup\Testimonials\Model\ResourceModel\Data\Collection
     */
    public function getTestimonials()
    {
        if (!$this->hasData('testimonials')) {
            $storeId = $this->_storeManager->getStore()->getId();
            $testimonials = $this->_testimonialsCollectionFactory
                ->create()
                ->addStatusFilter(TestimonialsModel::STATUS_ENABLED)
                ->addRatingFilter()
                ->addStoreFilter($storeId);

            $this->setData('testimonials', $testimonials);
        }
        return $this->getData('testimonials');
    }
    public function getListUrl()
    {
        return $this->getUrl('testimonials/index/index');
    }
    public function getStoreName()
    {
        $storeName = $this->_scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeName ? $storeName : $this->_storeManager->getStore()->getName();
    }
    /**
     * Get rating value in percents
     */
    public function getAvgRating()
    {
        $testimonials = $this->getTestimonials();
        if ($testimonials) {
            $total = 0;
            foreach ($testimonials as $testimonial) {
                $total += (int)$testimonial->getRating();
            }
            $avgRating = $total / $testimonials->getSize();
        }
        return number_format($avgRating, 2);
    }
}
