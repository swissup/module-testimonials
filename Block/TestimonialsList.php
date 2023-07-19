<?php
namespace Swissup\Testimonials\Block;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;
use Swissup\Testimonials\Model\Resolver\DataProvider\Testimonials as DataProvider;

class TestimonialsList extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Magento\Widget\Block\BlockInterface
{
    /**
     * Default template to use for review widget
     */
    const DEFAULT_LIST_TEMPLATE = 'list.phtml';

    /**
     * Default placeholder for empty profile image
     */
    const PROFILE_IMAGE_PLACEHOLDER = 'Swissup_Testimonials::images/empty.svg';

    /**
     * Contacts icons
     */
    const EMAIL_ICON = 'Swissup_Testimonials::images/email.png';
    const FACEBOOK_ICON = 'Swissup_Testimonials::images/facebook.png';
    const TWITTER_ICON = 'Swissup_Testimonials::images/twitter.png';

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    private $configHelper;

    /**
     * Get testimonials list helper
     * @var \Swissup\Testimonials\Helper\ListHelper
     */
    private $listHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param DataProvider $dataProvider
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Swissup\Testimonials\Helper\ListHelper $listHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        DataProvider $dataProvider,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Swissup\Testimonials\Helper\ListHelper $listHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
        $this->configHelper = $configHelper;
        $this->listHelper = $listHelper;
    }

    public function _construct()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_LIST_TEMPLATE);
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
                ->addStoreFilter([\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId])
                ->setOrder(
                    DataInterface::DATE,
                    TestimonialsCollection::SORT_ORDER_DESC
                )
                ->setCurPage($this->getCurrentPage())
                ->setPageSize($this->configHelper->getTestimonialsPerPage())
            ;
            $collection = $dataProvider->getCollection();

            $this->setData('testimonials', $collection);
        }

        return $this->getData('testimonials');
    }

    /**
     * @return boolean
     */
    public function isLastPage()
    {
        $testimonials = $this->getTestimonials();
        $curPage = $testimonials->getCurPage();
        $pageSize = $testimonials->getPageSize();

        return $curPage * $pageSize >= $testimonials->getSize();
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
        $identities = [];
        $testimonials = $this->getTestimonials();
        foreach ($testimonials as $testimonial) {
            $identities[] = $testimonial->getIdentities();
        }

        $identities[] = [TestimonialsModel::CACHE_TAG];

        return array_unique(array_merge([], ...$identities));
    }

    /**
     * Get user profile image url
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getProfileImageUrl($testimonial)
    {
        $image = $this->listHelper->resize($testimonial);

        return $image ? $image : $this->getViewFileUrl(self::PROFILE_IMAGE_PLACEHOLDER);
    }

    /**
     * @return string
     */
    public function getEmailIconUrl()
    {
        return $this->getViewFileUrl(self::EMAIL_ICON);
    }

    /**
     * @return string
     */
    public function getFacebookIconUrl()
    {
        return $this->getViewFileUrl(self::FACEBOOK_ICON);
    }

    /**
     * @return string
     */
    public function getTwitterIconUrl()
    {
        return $this->getViewFileUrl(self::TWITTER_ICON);
    }

    /**
     * Get rating value in percents
     * @param  \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getRatingPercent($testimonial)
    {
        return $this->listHelper->getRatingPercent($testimonial);
    }
}
