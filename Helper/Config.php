<?php
namespace Swissup\Testimonials\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * Path to store config of testimonial auto approve
     *
     * @var string
     */
    const XML_PATH_APPROVE            = 'testimonials/general/approve';
    /**
     * Path to store config for testimonials list layout
     *
     * @var string
     */
    const XML_PATH_LIST_LAYOUT           = 'testimonials/list/layout';
    /**
     * Path to store config where count of news posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'testimonials/list/items_per_page';
    /**
     * Path to store config testimonial image width
     *
     * @var string
     */
    const XML_PATH_IMAGE_W            = 'testimonials/list/image_width';
    /**
     * Path to store config testimonial image height
     *
     * @var string
     */
    const XML_PATH_IMAGE_H            = 'testimonials/list/image_height';
    /**
     * Path to store config for placeholder image
     *
     * @var string
     */
    const XML_PATH_PLACEHOLDER_IMAGE           = 'testimonials/list/placeholder_image';
    /**
     * Path to store config for show user email in list
     *
     * @var string
     */
    const XML_PATH_LIST_EMAIL           = 'testimonials/list/show_email';
    /**
     * Path to store config for testimonials form layout
     *
     * @var string
     */
    const XML_PATH_FORM_LAYOUT           = 'testimonials/form/layout';
    /**
     * Path to store config company field enabled
     *
     * @var string
     */
    const XML_PATH_COMPANY_ENABLED            = 'testimonials/form/enable_company';
    /**
     * Path to store config website field enabled
     *
     * @var string
     */
    const XML_PATH_WEBSITE_ENABLED            = 'testimonials/form/enable_website';
    /**
     * Path to store config twitter field enabled
     *
     * @var string
     */
    const XML_PATH_TWITTER_ENABLED            = 'testimonials/form/enable_twitter';
    /**
     * Path to store config facebook field enabled
     *
     * @var string
     */
    const XML_PATH_FACEBOOK_ENABLED            = 'testimonials/form/enable_facebook';
    /**
     * Path to store config sent message
     *
     * @var string
     */
    const XML_PATH_SENT_MESSAGE                = 'testimonials/form/sent_message';
    /**
     * Path to store config admin email notification enable
     *
     * @var string
     */
    const XML_PATH_ADMIN_EMAIL_ENABLED                = 'testimonials/email_admin/send_enable';
    /**
     * Path to store config send email for admin from
     *
     * @var string
     */
    const XML_PATH_ADMIN_EMAIL_SEND_FROM              = 'testimonials/email_admin/send_from';
    /**
     * Path to store config admin email
     *
     * @var string
     */
    const XML_PATH_ADMIN_EMAIL              = 'testimonials/email_admin/admin_email';
    /**
     * Path to store config admin email subject
     *
     * @var string
     */
    const XML_PATH_ADMIN_EMAIL_SUBJECT              = 'testimonials/email_admin/email_subject';
    /**
     * Path to store config admin email template
     *
     * @var string
     */
    const XML_PATH_ADMIN_EMAIL_TEMPLATE              = 'testimonials/email_admin/email_template';

    protected function _getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }
    public function isAutoApprove()
    {
        return (bool)$this->_getConfig(self::XML_PATH_APPROVE);
    }
    public function getListLayout()
    {
        return (String)$this->_getConfig(self::XML_PATH_LIST_LAYOUT);
    }
    public function getTestimonialsPerPage()
    {
        return abs((int)$this->_getConfig(self::XML_PATH_ITEMS_PER_PAGE));
    }
    public function getImageWidth()
    {
        return abs((int)$this->_getConfig(self::XML_PATH_IMAGE_W));
    }
    public function getImageHeight()
    {
        return abs((int)$this->_getConfig(self::XML_PATH_IMAGE_H));
    }
    public function getPlaceholderImage()
    {
        return (String)$this->_getConfig(self::XML_PATH_PLACEHOLDER_IMAGE);
    }
    public function showUserEmail()
    {
        return (bool)$this->_getConfig(self::XML_PATH_LIST_EMAIL);
    }
    public function getFormLayout()
    {
        return (String)$this->_getConfig(self::XML_PATH_FORM_LAYOUT);
    }
    public function isCompanyEnabled()
    {
        return (bool)$this->_getConfig(self::XML_PATH_COMPANY_ENABLED);
    }
    public function isWebsiteEnabled()
    {
        return (bool)$this->_getConfig(self::XML_PATH_WEBSITE_ENABLED);
    }
    public function isTwitterEnabled()
    {
        return (bool)$this->_getConfig(self::XML_PATH_TWITTER_ENABLED);
    }
    public function isFacebookEnabled()
    {
        return (bool)$this->_getConfig(self::XML_PATH_FACEBOOK_ENABLED);
    }
    public function getSentMessage()
    {
        return (String)$this->_getConfig(self::XML_PATH_SENT_MESSAGE);
    }
    public function isAdminNotificationEnabled()
    {
        return (bool)$this->_getConfig(self::XML_PATH_ADMIN_EMAIL_ENABLED);
    }
    public function getAdminNotificationSendFrom()
    {
        return (String)$this->_getConfig(self::XML_PATH_ADMIN_EMAIL_SEND_FROM);
    }
    public function getAdminEmail()
    {
        return (String)$this->_getConfig(self::XML_PATH_ADMIN_EMAIL);
    }
    public function getAdminEmailSubject()
    {
        return (String)$this->_getConfig(self::XML_PATH_ADMIN_EMAIL_SUBJECT);
    }
    public function getAdminEmailTemplate()
    {
        return (String)$this->_getConfig(self::XML_PATH_ADMIN_EMAIL_TEMPLATE);
    }
}