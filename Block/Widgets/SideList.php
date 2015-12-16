<?php
namespace Swissup\Testimonials\Block\Widgets;

use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
/**
 * Class side list widget
 * @package Swissup\Testimonials\Block\Widgets
 */
class SideList extends \Magento\Framework\View\Element\Template
     implements \Magento\Widget\Block\BlockInterface
{
    const DEFAULT_LIST_TEMPLATE = 'widgets/sidelist.phtml';
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory,
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
            $this->setData('template', self::DEFAULT_LIST_TEMPLATE);
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
                ->addWidgetFilter(1)
                ->addStoreFilter($storeId);

            $testimonials->getSelect()
                    ->order(new \Zend_Db_Expr('RAND()'))
                    ->limit($this->getItemsNumber());

            $this->setData('testimonials', $testimonials);
        }
        return $this->getData('testimonials');
    }
    public function getListUrl()
    {
        return $this->getUrl('testimonials/index/index');
    }
    /**
     * Get rating value in percents
     * @param  \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getRatingPercent($testimonial)
    {
        $ratingPercent = $testimonial->getRating() / 5 * 100;
        return (String)$ratingPercent;
    }
}
