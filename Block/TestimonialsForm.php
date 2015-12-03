<?php
namespace Swissup\Testimonials\Block;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;

class TestimonialsForm extends \Magento\Framework\View\Element\Template implements
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
     * Get form action
     * @return String form action url
     */
    public function getFormAction()
    {
        return $this->getUrl('testimonials/index/save');
    }
    /**
     * Get list action
     * @return String form action url
     */
    public function getListAction()
    {
        return $this->getUrl('testimonials/index/index');
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [TestimonialsModel::CACHE_TAG . '_' . 'form'];
    }
}