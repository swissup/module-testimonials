<?php
namespace Swissup\Testimonials\Block;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;

class TestimonialsList extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface
{
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
     /**
     * @return \Swissup\Testimonials\Model\ResourceModel\Data\Collection
     */
    public function getTestimonials()
    {
        if (!$this->hasData('testimonials')) {
            $testimonials = $this->_testimonialsCollectionFactory
                ->create()
                ->addOrder(
                    DataInterface::DATE,
                    TestimonialsCollection::SORT_ORDER_DESC
                );
            $this->setData('testimonials', $testimonials);
        }
        return $this->getData('testimonials');
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Swissup\Testimonials\Model\Data::CACHE_TAG . '_' . 'list'];
    }
}