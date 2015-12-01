<?php
namespace Swissup\Testimonials\Block;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;

class TestimonialsList extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    public $configHelper;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory,
     * @param \Swissup\Testimonials\Helper\Config $configHelper,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory,
        \Swissup\Testimonials\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_testimonialsCollectionFactory = $testimonialsCollectionFactory;
        $this->configHelper = $configHelper;
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
                ->addStoreFilter($storeId)
                ->addOrder(
                    DataInterface::DATE,
                    TestimonialsCollection::SORT_ORDER_DESC
                )
                ->setCurPage(1)
                ->setPageSize($this->configHelper->getTestimonialsPerPage());
            $this->setData('testimonials', $testimonials);
        }
        return $this->getData('testimonials');
    }

    public function getNewAction()
    {
        return $this->getUrl('testimonials/index/new');
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [TestimonialsModel::CACHE_TAG . '_' . 'list'];
    }
}