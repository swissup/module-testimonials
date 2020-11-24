<?php
namespace Swissup\Testimonials\Block\Widgets;

use Swissup\Testimonials\Model\Data as TestimonialsModel;

class Slider extends \Magento\Framework\View\Element\Template
     implements \Magento\Widget\Block\BlockInterface
{
    const DEFAULT_TEMPLATE = 'widgets/slider.phtml';
    const DEFAULT_TITLE = 'What Clients Say';
    const DEFAULT_VISIBLE_SLIDES = 2;

    /**
     * @var \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory
     */
    private $testimonialsCollectionFactory;

    /**
     * Get testimonials list helper
     * @var \Swissup\Testimonials\Helper\ListHelper
     */
    private $listHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory
     * @param \Swissup\Testimonials\Helper\ListHelper $listHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory,
        \Swissup\Testimonials\Helper\ListHelper $listHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->testimonialsCollectionFactory = $testimonialsCollectionFactory;
        $this->listHelper = $listHelper;
    }

    public function _construct()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_TEMPLATE);
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
            $testimonials = $this->testimonialsCollectionFactory
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
            "freeMode" => true,
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
