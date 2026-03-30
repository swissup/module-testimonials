<?php
namespace Swissup\Testimonials\Api\Data;

interface DataInterface
{
    const TESTIMONIAL_ID = 'testimonial_id';
    const STATUS = 'status';
    const DATE = 'date';
    const NAME = 'name';
    const EMAIL = 'email';
    const MESSAGE = 'message';
    const COMPANY = 'company';
    const WEBSITE = 'website';
    const TWITTER = 'twitter';
    const FACEBOOK = 'facebook';
    const IMAGE = 'image';
    const RATING = 'rating';
    const WIDGET = 'widget';

    /**
     * Get testimonial_id
     *
     * @return int
     */
    public function getTestimonialId();

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get date
     *
     * @return string
     */
    public function getDate();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany();

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite();

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter();

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook();

    /**
     * Get image
     *
     * @return string
     */
    public function getImage();

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating();

    /**
     * Get widget
     *
     * @return int
     */
    public function getWidget();


    /**
     * Set testimonial_id
     *
     * @param int $testimonialId
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setTestimonialId($testimonialId);

    /**
     * Set status
     *
     * @param int $status
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setStatus($status);

    /**
     * Set date
     *
     * @param string $date
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setDate($date);

    /**
     * Set name
     *
     * @param string $name
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setName($name);

    /**
     * Set email
     *
     * @param string $email
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setEmail($email);

    /**
     * Set message
     *
     * @param string $message
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setMessage($message);

    /**
     * Set company
     *
     * @param string $company
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setCompany($company);

    /**
     * Set website
     *
     * @param string $website
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setWebsite($website);

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setTwitter($twitter);

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setFacebook($facebook);

    /**
     * Set image
     *
     * @param string $image
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setImage($image);

    /**
     * Set rating
     *
     * @param int $rating
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setRating($rating);

    /**
     * Set widget
     *
     * @param int $widget
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     */
    public function setWidget($widget);

}