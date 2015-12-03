<?php
namespace Swissup\Testimonials\Controller\Index;

use Swissup\Testimonials\Api\Data\DataInterface;
use Magento\Store\Model\ScopeInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $testimonialSession;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    protected $_configHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Magento\Framework\Session\Generic $testimonialSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Magento\Framework\Session\Generic $testimonialSession
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_configHelper = $configHelper;
        $this->testimonialSession = $testimonialSession;
    }
    protected function _redirectReferer()
    {
        $this->_redirect($this->_redirect->getRedirectUrl());
    }
    /**
     * Save user testimonial
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirectReferer();
            return;
        }
        try {
            $error = false;
            if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['message']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }
            if ($error) {
                throw new \Exception();
            }
            $post['store_id'] = $this->_storeManager->getStore()->getId();
            $post['status'] = $this->_configHelper->isAutoApprove() ?
                TestimonialsModel::STATUS_ENABLED :
                TestimonialsModel:: STATUS_AWAITING_APPROVAL;
            $model = $this->_objectManager->create('Swissup\Testimonials\Model\Data');
            $model->setData($post);
            $model->save();
            $this->messageManager->addSuccess(__($this->_configHelper->getSentMessage()));
            $this->_redirectReferer();
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
            $this->testimonialSession->setFormData(
                $post
            )->setRedirectUrl(
                $this->_redirect->getRefererUrl()
            );
            $this->_redirectReferer();
            return;
        }
    }
}