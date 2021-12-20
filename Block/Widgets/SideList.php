<?php
namespace Swissup\Testimonials\Block\Widgets;

use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
use Swissup\Testimonials\Model\Resolver\DataProvider\Testimonials as DataProvider;
/**
 * Class side list widget
 * @package Swissup\Testimonials\Block\Widgets
 */
class SideList extends \Magento\Framework\View\Element\Template
     implements \Magento\Widget\Block\BlockInterface
{
    const DEFAULT_LIST_TEMPLATE = 'widgets/sidelist.phtml';

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param DataProvider $dataProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        DataProvider $dataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
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
            $dataProvider = $this->dataProvider
                ->addStatusFilter(TestimonialsModel::STATUS_ENABLED)
                ->addWidgetFilter(1)
                ->addStoreFilter([\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId])
                ->setRandomOrder()
                ->setCurPage(1)
                ->setPageSize($this->getItemsNumber())
            ;
            $collection = $dataProvider->getCollection();

            $this->setData('testimonials', $collection);
        }
        return $this->getData('testimonials');
    }
    public function getListUrl()
    {
        return $this->getUrl('testimonials');
    }
}
