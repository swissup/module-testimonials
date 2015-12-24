<?php
namespace Swissup\Testimonials\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;

/**
 * Testimonials form helper
 */
class Form extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;
    /**
     * Testimonials form data
     */
    protected $_data;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerViewHelper $customerViewHelper
     * @param \Magento\Framework\Session\Generic $testimonialSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerViewHelper $customerViewHelper,
        \Magento\Framework\Session\Generic $testimonialSession
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerViewHelper = $customerViewHelper;
        $this->_data = $testimonialSession->getFormData(true);
        parent::__construct($context);
    }
    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        if (!empty($this->_data['name'])) {
            return $this->_data['name'];
        }
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();
        return trim($this->_customerViewHelper->getCustomerName($customer));
    }
    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if (!empty($this->_data['email'])) {
            return $this->_data['email'];
        }
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var CustomerInterface $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();
        return $customer->getEmail();
    }
    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->_data['company'];
    }
    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->_data['website'];
    }
    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->_data['twitter'];
    }
    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->_data['facebook'];
    }
    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_data['message'];
    }
    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return isset($this->_data['rating']) ? $this->_data['rating'] : -1;
    }
}