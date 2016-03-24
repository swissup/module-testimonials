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
    protected $_testimonialSession;
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
     * upload model
     *
     * @var \Swissup\Testimonials\Model\Upload
     */
    protected $_uploadModel;
    /**
     * image model
     *
     * @var \Swissup\Testimonials\Model\Data\Image
     */
    protected $_imageModel;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Magento\Framework\Session\Generic $testimonialSession
     * @param \Swissup\Testimonials\Model\Data\Image $imageModel
     * @param \Swissup\Testimonials\Model\Upload $uploadModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Magento\Framework\Session\Generic $testimonialSession,
        \Swissup\Testimonials\Model\Data\Image $imageModel,
        \Swissup\Testimonials\Model\Upload $uploadModel
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_configHelper = $configHelper;
        $this->_testimonialSession = $testimonialSession;
        $this->_uploadModel = $uploadModel;
        $this->_imageModel = $imageModel;
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
            $imageName = $this->_uploadModel
                ->uploadFileAndGetName('image',
                    $this->_imageModel->getBaseDir(),
                    $post,
                    ['jpg','jpeg','gif','png', 'bmp']
                );
            $model->setImage($imageName);
            $this->_eventManager->dispatch('testimonials_save_new', ['item' => $model]);
            $model->save();
            $this->messageManager->addSuccess(__($this->_configHelper->getSentMessage()));
            $this->_redirectReferer();
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->_testimonialSession->setFormData(
                $post
            )->setRedirectUrl(
                $this->_redirect->getRefererUrl()
            );
            $this->_redirectReferer();
            return;
        }
    }
}