<?php
namespace Swissup\Testimonials\Block\Widgets;

use Swissup\Testimonials\Model\Data as TestimonialsModel;
use Swissup\Testimonials\Model\Resolver\DataProvider\Testimonials as DataProvider;

class Slider extends \Magento\Framework\View\Element\Template
     implements \Magento\Widget\Block\BlockInterface
{
    const DEFAULT_TEMPLATE = 'widgets/slider.phtml';
    const DEFAULT_TITLE = 'What Clients Say';
    const DEFAULT_VISIBLE_SLIDES = 2;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * Get testimonials list helper
     * @var \Swissup\Testimonials\Helper\ListHelper
     */
    private $listHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param DataProvider $dataProvider
     * @param \Swissup\Testimonials\Helper\ListHelper $listHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        DataProvider $dataProvider,
        \Swissup\Testimonials\Helper\ListHelper $listHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
        $this->listHelper = $listHelper;
    }

    public function _construct()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_TEMPLATE);
        }

        parent::_construct();
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
                ->addWidgetFilter(true)
                ->addStoreFilter([\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId])
                ->setRandomOrder(true)
                ->setCurPage(1)
                ->setPageSize($this->getItemsNumber())
            ;
            $collection = $dataProvider->getCollection();

            $this->setData('testimonials', $collection);
        }

        return $this->getData('testimonials');
    }

    /**
     * @return false|string
     */
    public function getDataProviderConfig()
    {
       return json_encode($this->dataProvider->getConfig(), JSON_HEX_APOS);
    }

    /**
     * Get user profile image url
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getProfileImageUrl($testimonial)
    {
        $image = $this->listHelper
            ->resize($testimonial, $this->getImgWidth(), $this->getImgHeight());

        return $image ? $image : $this->getViewFileUrl('Swissup_Testimonials::images/empty.svg');
    }

    /**
     * Get block title
     * @return String
     */
    public function getBlockTitle()
    {
        return $this->getTitle() ? __($this->getTitle()) : __(self::DEFAULT_TITLE);
    }

    /**
     * Get slides to show
     * @return int
     */
    public function getVisibleSlides()
    {
        return $this->getSlidesToShow() ? : self::DEFAULT_VISIBLE_SLIDES;
    }

    /**
     * Get slider config
     * @return String
     */
    public function getSwiperConfig()
    {
        $params = [
            "slidesPerView" => $this->getVisibleSlides(),
            "slidesToScroll" => 1,
            "freeMode" => false,
            "loop" => true,
            'navigation' => [
                'nextEl' => '.swiper-button-next',
                'prevEl' => '.swiper-button-prev'
            ],
            "breakpoints" => [
                '1024' => [
                    "slidesPerView" => 1
                ]
            ]
        ];

        return json_encode($params, JSON_HEX_APOS);
    }

    /**
     * Get rating value in percents
     * @param  \Swissup\Testimonials\Model\Data $testimonial
     * @return String|bool
     */
    public function getRatingPercent($testimonial)
    {
        if ($this->getShowRating() && $testimonial->getRating()) {
            return $this->listHelper->getRatingPercent($testimonial);
        }

        return false;
    }
}
