<?php
namespace Swissup\Testimonials\Block;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;

class TestimonialsList extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Magento\Widget\Block\BlockInterface
{
    /**
     * Default template to use for review widget
     */
    const DEFAULT_LIST_TEMPLATE = 'list.phtml';
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    public $configHelper;
    /**
     * Get testimonials list helper
     * @var \Swissup\Testimonials\Helper\ListHelper
     */
    public $listHelper;
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
        \Swissup\Testimonials\Helper\ListHelper $listHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_testimonialsCollectionFactory = $testimonialsCollectionFactory;
        $this->configHelper = $configHelper;
        $this->listHelper = $listHelper;
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
                ->addStoreFilter($storeId)
                ->addOrder(
                    DataInterface::DATE,
                    TestimonialsCollection::SORT_ORDER_DESC
                )
                ->setCurPage($this->getCurrentPage())
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
     * Get load action
     * @return String load action url
     */
    public function getLoadAction()
    {
        return $this->getUrl('testimonials/index/load');
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
    /**
     * Get user profile image url
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getProfileImageUrl($testimonial)
    {
        $image = $this->listHelper->resize($testimonial);
        return $image ? $image : $this->getViewFileUrl('Swissup_Testimonials::images/empty.svg');
    }
    /**
     * Check if social block enabled
     * @param  \Swissup\Testimonials\Model\Data $testimonial
     * @return Boolean
     */
    public function canShowSocial($testimonial)
    {
        return (($testimonial->getFacebook() && $this->configHelper->isFacebookEnabled())||
                ($testimonial->getTwitter() && $this->configHelper->isTwitterEnabled()));
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