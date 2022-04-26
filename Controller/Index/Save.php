<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;

class Save extends \Magento\Framework\App\Action\Action
{
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
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    protected $testimonialsFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Swissup\Core\Api\Media\FileInfoInterface $imageModel
     * @param \Swissup\Testimonials\Model\Upload $uploadModel
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Swissup\Core\Api\Media\FileInfoInterface $imageModel,
        \Swissup\Testimonials\Model\Upload $uploadModel,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory,
        \Magento\Customer\Model\Session $customerSession,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->uploadModel = $uploadModel;
        $this->imageModel = $imageModel;
        $this->testimonialsFactory = $testimonialsFactory;
        $this->customerSession = $customerSession;
        $this->dataPersistor = $dataPersistor;
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

    /**
     * Save user testimonial
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$post) {
            return $resultRedirect->setRefererUrl();
        }

        try {
            if (!$this->validate($post)) {
                throw new \Exception(__('Please fill all required fields.'));
            }
            $post['store_id'] = $this->storeManager->getStore()->getId();
            $post['status'] = $this->configHelper->isAutoApprove() ?
                TestimonialsModel::STATUS_ENABLED :
                TestimonialsModel:: STATUS_AWAITING_APPROVAL;
            $model = $this->testimonialsFactory->create();
            $model->setData($post);
            if (isset($post['image'])) {
                $imageName = $this->uploadModel
                    ->uploadFileAndGetName('image',
                        $this->imageModel->getBaseDir(),
                        $post,
                        ['jpg','jpeg','gif','png', 'bmp']
                    );
                $model->setImage($imageName);
            }
            $this->_eventManager->dispatch('testimonials_save_new', ['item' => $model]);
            $model->save();
            $this->messageManager->addSuccess(__($this->configHelper->getSentMessage()));
            $this->dataPersistor->clear('testimonials_form_data');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->dataPersistor->set('testimonials_form_data', $post);
        }

        return $resultRedirect->setRefererUrl();
    }

    /**
     * Validate form data
     *
     * @param  array $data
     * @return bool
     */
    protected function validate($data)
    {
        $valid = true;
        if (!\Zend_Validate::is(trim($data['name']), 'NotEmpty')) {
            $valid = false;
        }
        if (!\Zend_Validate::is(trim($data['message']), 'NotEmpty')) {
            $valid = false;
        }
        if (!\Zend_Validate::is(trim($data['email']), 'EmailAddress')) {
            $valid = false;
        }
        if ($this->configHelper->isRatingRequired() && (!isset($data['rating']) ||
            !\Zend_Validate::is(trim($data['rating']), 'NotEmpty')))
        {
            $valid = false;
        }

        return $valid;
    }
}
