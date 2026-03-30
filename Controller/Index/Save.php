<?php
namespace Swissup\Testimonials\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Validator\EmailAddress as EmailAddressValidator;
use Magento\Framework\Validator\NotEmpty;
use Magento\Framework\Validator\NotEmptyFactory;
use Swissup\Testimonials\Api\TestimonialRepositoryInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;

class Save implements HttpPostActionInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Swissup\Testimonials\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Swissup\Testimonials\Model\Upload
     */
    private $uploadModel;

    /**
     * @var \Swissup\Core\Api\Media\FileInfoInterface
     */
    private $imageModel;

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    private $testimonialsFactory;

    /**
     * @var TestimonialRepositoryInterface
     */
    private $testimonialRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var NotEmpty
     */
    private $notEmpty;

    /**
     * @var EmailAddressValidator
     */
    private $emailAddressValidator;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Swissup\Core\Api\Media\FileInfoInterface $imageModel
     * @param \Swissup\Testimonials\Model\Upload $uploadModel
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     * @param TestimonialRepositoryInterface $testimonialRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param DataPersistorInterface $dataPersistor
     * @param NotEmptyFactory $notEmptyFactory
     * @param EmailAddressValidator $emailAddressValidator
     * @param ResultFactory $resultFactory
     * @param EventManagerInterface $eventManager
     * @param MessageManagerInterface $messageManager
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Swissup\Core\Api\Media\FileInfoInterface $imageModel,
        \Swissup\Testimonials\Model\Upload $uploadModel,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory,
        TestimonialRepositoryInterface $testimonialRepository,
        \Magento\Customer\Model\Session $customerSession,
        DataPersistorInterface $dataPersistor,
        NotEmptyFactory $notEmptyFactory,
        EmailAddressValidator $emailAddressValidator,
        ResultFactory $resultFactory,
        EventManagerInterface $eventManager,
        MessageManagerInterface $messageManager,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->uploadModel = $uploadModel;
        $this->imageModel = $imageModel;
        $this->testimonialsFactory = $testimonialsFactory;
        $this->testimonialRepository = $testimonialRepository;
        $this->customerSession = $customerSession;
        $this->dataPersistor = $dataPersistor;
        $this->notEmpty = $notEmptyFactory->create(['options' => NotEmpty::ALL]);
        $this->emailAddressValidator = $emailAddressValidator;
        $this->resultFactory = $resultFactory;
        $this->eventManager = $eventManager;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Save user testimonial
     *
     * @return \Magento\Framework\Controller\Result\Redirect|ResponseInterface
     */
    public function execute()
    {
        if (!$this->configHelper->guestSubmitAllowed() &&
            !$this->customerSession->authenticate()
        ) {
            // authenticate() redirects the customer to the login page
            // and returns false; return the response to complete the redirect.
            return $this->response;
        }

        $post = $this->request->getPostValue();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$post) {
            return $resultRedirect->setRefererUrl();
        }

        try {
            if (!$this->validate($post)) {
                throw new \Exception(__('Please fill all required fields.'));
            }

            // Only accept user-facing fields; status and widget are set server-side
            $allowedFields = ['name', 'email', 'message', 'company', 'website', 'twitter', 'facebook', 'rating'];
            $safeData = array_intersect_key($post, array_flip($allowedFields));

            $safeData['store_id'] = $this->storeManager->getStore()->getId();
            $safeData['status'] = $this->configHelper->isAutoApprove() ?
                TestimonialsModel::STATUS_ENABLED :
                TestimonialsModel::STATUS_AWAITING_APPROVAL;
            $safeData['widget'] = 1;

            $model = $this->testimonialsFactory->create();
            $model->setData($safeData);
            if ($image = $this->request->getFiles('image')) {
                $imageName = $this->uploadModel->uploadFileAndGetName(
                    'image',
                    $this->imageModel->getBaseDir(),
                    $image,
                    ['jpg', 'jpeg', 'gif', 'png', 'bmp']
                );
                $model->setImage($imageName);
            }
            $this->eventManager->dispatch('testimonials_save_new', ['item' => $model]);
            $this->testimonialRepository->save($model);
            $this->messageManager->addSuccessMessage($this->configHelper->getSentMessage());
            $this->dataPersistor->clear('testimonials_form_data');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('testimonials_form_data', $post);
        }

        return $resultRedirect->setRefererUrl();
    }

    /**
     * Get request object
     *
     * Required for predispatch event observers (e.g. CAPTCHA, reCAPTCHA)
     * that call $controller->getRequest() on the controller action.
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Get response object
     *
     * Required for predispatch event observers (e.g. CAPTCHA, reCAPTCHA)
     * that call $controller->getResponse() on the controller action.
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Validate form data
     *
     * @param array $data
     * @return bool
     */
    private function validate(array $data): bool
    {
        if (!$this->notEmpty->isValid(trim($data['name'] ?? ''))) {
            return false;
        }
        if (!$this->notEmpty->isValid(trim($data['message'] ?? ''))) {
            return false;
        }
        if (empty(trim($data['email'] ?? '')) || !$this->emailAddressValidator->isValid($data['email'] ?? '')) {
            return false;
        }
        if ($this->configHelper->isRatingRequired() &&
            (!isset($data['rating']) || !$this->notEmpty->isValid(trim($data['rating'])))
        ) {
            return false;
        }

        return true;
    }
}
