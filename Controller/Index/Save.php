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
    protected $storeManager;

    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    protected $configHelper;

    /**
     * upload model
     *
     * @var \Swissup\Testimonials\Model\Upload
     */
    protected $uploadModel;

    /**
     * image model
     *
     * @var \Swissup\Core\Api\Media\FileInfoInterface
     */
    protected $imageModel;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    protected $testimonialsFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Magento\Framework\Session\Generic $testimonialSession
     * @param \Swissup\Core\Api\Media\FileInfoInterface $imageModel
     * @param \Swissup\Testimonials\Model\Upload $uploadModel
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Magento\Framework\Session\Generic $testimonialSession,
        \Swissup\Core\Api\Media\FileInfoInterface $imageModel,
        \Swissup\Testimonials\Model\Upload $uploadModel,
        \Magento\Customer\Model\Session $customerSession,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->testimonialSession = $testimonialSession;
        $this->uploadModel = $uploadModel;
        $this->imageModel = $imageModel;
        $this->customerSession = $customerSession;
        $this->testimonialsFactory = $testimonialsFactory;
    }

    /**
     * Check customer authentication
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$request->isDispatched()) {
            return parent::dispatch($request);
        }

        if (!$this->configHelper->guestSubmitAllowed() &&
            !$this->customerSession->authenticate()
        ) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    protected function redirectReferer()
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
            $this->redirectReferer();

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
            if ($this->configHelper->isRatingRequired() && (!isset($post['rating']) ||
                !\Zend_Validate::is(trim($post['rating']), 'NotEmpty')))
            {
                $error = true;
            }
            if ($error) {
                throw new \Exception(__('Please fill all required fields.'));
            }
            $post['store_id'] = $this->storeManager->getStore()->getId();
            $post['status'] = $this->configHelper->isAutoApprove() ?
                TestimonialsModel::STATUS_ENABLED :
                TestimonialsModel:: STATUS_AWAITING_APPROVAL;
            $model = $this->testimonialsFactory->create();
            $model->setData($post);
            $imageName = $this->uploadModel
                ->uploadFileAndGetName('image',
                    $this->imageModel->getBaseDir(),
                    $post,
                    ['jpg','jpeg','gif','png', 'bmp']
                );
            $model->setImage($imageName);
            $this->_eventManager->dispatch('testimonials_save_new', ['item' => $model]);
            $model->save();
            $this->messageManager->addSuccess(__($this->configHelper->getSentMessage()));
            $this->redirectReferer();

            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->testimonialSession->setFormData(
                $post
            )->setRedirectUrl(
                $this->_redirect->getRefererUrl()
            );
            $this->redirectReferer();

            return;
        }
    }
}
