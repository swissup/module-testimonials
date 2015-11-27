<?php
namespace Swissup\Testimonials\Model;

use Swissup\Testimonials\Api\Data\DataInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Data extends \Magento\Framework\Model\AbstractModel implements DataInterface, IdentityInterface
{
    /**
     * Testimonial's Statuses
     */
    const STATUS_AWAITING_APPROVAL = 1;
    const STATUS_ENABLED = 2;
    const STATUS_DISABLED = 3;

    /**
     * cache tag
     */
    const CACHE_TAG = 'testimonials_data';

    /**
     * @var string
     */
    protected $_cacheTag = 'testimonials_data';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'testimonials_data';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\Testimonials\Model\ResourceModel\Data');
    }

    /**
     * Prepare testimonials's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_AWAITING_APPROVAL => __('Awaiting approval')
        ];
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getTestimonialId()];
    }

    /**
     * Get testimonial_id
     *
     * return int
     */
    public function getTestimonialId()
    {
        return $this->getData(self::TESTIMONIAL_ID);
    }

    /**
     * Get status
     *
     * return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get date
     *
     * return string
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * Get name
     *
     * return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get email
     *
     * return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Get message
     *
     * return string
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Get company
     *
     * return string
     */
    public function getCompany()
    {
        return $this->getData(self::COMPANY);
    }

    /**
     * Get website
     *
     * return string
     */
    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    /**
     * Get twitter
     *
     * return string
     */
    public function getTwitter()
    {
        return $this->getData(self::TWITTER);
    }

    /**
     * Get facebook
     *
     * return string
     */
    public function getFacebook()
    {
        return $this->getData(self::FACEBOOK);
    }

    /**
     * Get image
     *
     * return string
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Get rating
     *
     * return int
     */
    public function getRating()
    {
        return $this->getData(self::RATING);
    }

    /**
     * Get widget
     *
     * return int
     */
    public function getWidget()
    {
        return $this->getData(self::WIDGET);
    }

    /**
     * Set testimonial_id
     *
     * @param int $testimonialId
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setTestimonialId($testimonialId)
    {
        return $this->setData(self::TESTIMONIAL_ID, $testimonialId);
    }

    /**
     * Set status
     *
     * @param int $status
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set date
     *
     * @param string $date
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }

    /**
     * Set name
     *
     * @param string $name
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set email
     *
     * @param string $email
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Set message
     *
     * @param string $message
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Set company
     *
     * @param string $company
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * Set website
     *
     * @param string $website
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setTwitter($twitter)
    {
        return $this->setData(self::TWITTER, $twitter);
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setFacebook($facebook)
    {
        return $this->setData(self::FACEBOOK, $facebook);
    }

    /**
     * Set image
     *
     * @param string $image
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set rating
     *
     * @param int $rating
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setRating($rating)
    {
        return $this->setData(self::RATING, $rating);
    }

    /**
     * Set widget
     *
     * @param int $widget
     * return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setWidget($widget)
    {
        return $this->setData(self::WIDGET, $widget);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }
}