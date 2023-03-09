<?php
namespace Swissup\Testimonials\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

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
    protected $customerSession;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerViewHelper;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Testimonials form data
     */
    protected $data = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerViewHelper $customerViewHelper
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerViewHelper $customerViewHelper,
        DataPersistorInterface $dataPersistor
    ) {
        $this->customerSession = $customerSession;
        $this->customerViewHelper = $customerViewHelper;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        if ($name = $this->getPostValue('name')) {
            return $name;
        }
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->customerSession->getCustomerDataObject();

        return trim($this->customerViewHelper->getCustomerName($customer));
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if ($email = $this->getPostValue('email')) {
            return $email;
        }
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var CustomerInterface $customer
         */
        $customer = $this->customerSession->getCustomerDataObject();

        return $customer->getEmail();
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        if ($company = $this->getPostValue('company')) {
            return $company;
        }
        return '';
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        if ($website = $this->getPostValue('website')) {
            return $website;
        }
        return '';
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        if ($twitter = $this->getPostValue('twitter')) {
            return $twitter;
        }
        return '';
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        if ($facebook = $this->getPostValue('facebook')) {
            return $facebook;
        }
        return '';
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        if ($message = $this->getPostValue('message')) {
            return $message;
        }
        return '';
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return $this->getPostValue('rating') ?? -1;
    }

    /**
     * Get value from POST by key
     *
     * @param string $key
     * @return string
     */
    protected function getPostValue($key)
    {
        if ($this->data === null) {
            $this->data = (array) $this->dataPersistor->get('testimonials_form_data');
            $this->dataPersistor->clear('testimonials_form_data');
        }

        if (isset($this->data[$key])) {
            return (string) $this->data[$key];
        }

        return '';
    }
}
