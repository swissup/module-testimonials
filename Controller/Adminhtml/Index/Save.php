<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validator\EmailAddress as EmailAddressValidator;
use Swissup\Testimonials\Api\TestimonialRepositoryInterface;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Testimonials::save';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Swissup\Testimonials\ImageUpload
     */
    protected $imageUploader;

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    protected $testimonialsFactory;

    /**
     * @var TestimonialRepositoryInterface
     */
    private $testimonialRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var EmailAddressValidator
     */
    private $emailAddressValidator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Swissup\Testimonials\ImageUpload $imageUploader
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     * @param TestimonialRepositoryInterface $testimonialRepository
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param EmailAddressValidator $emailAddressValidator
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory,
        TestimonialRepositoryInterface $testimonialRepository,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        EmailAddressValidator $emailAddressValidator,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->imageUploader = $imageUploader;
        $this->testimonialsFactory = $testimonialsFactory;
        $this->testimonialRepository = $testimonialRepository;
        $this->dateFilter = $dateFilter;
        $this->emailAddressValidator = $emailAddressValidator;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $this->dataPersistor->set('testimonial_data', $data);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('testimonial_id');

            // Load existing or create new
            if ($id) {
                try {
                    $model = $this->testimonialRepository->getById((int)$id);
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(__('This testimonial no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $model = $this->testimonialsFactory->create();
            }

            if (empty($data['testimonial_id'])) {
                $data['testimonial_id'] = null;
            }

            if (!empty($data['date'])) {
                $data['date'] = $this->dateFilter->filter($data['date']);
            }

            // Only set the submitted fields to avoid overwriting unsubmitted ones.
            // 'stores' is intentionally excluded: the UI multiselect submits its value
            // under 'store_id' (dataScope="store_id"). Setting 'store_id' alone is
            // sufficient; but we must also unset the stale 'stores' array that
            // _afterLoad() placed on the model, otherwise Model\Data::getStores()
            // returns that old value and _afterSave() never updates the relation.
            $allowedFields = [
                'status', 'date', 'name', 'email', 'message',
                'company', 'website', 'twitter', 'facebook', 'rating', 'widget',
                'store_id', 'testimonial_id'
            ];
            foreach (array_intersect_key($data, array_flip($allowedFields)) as $key => $value) {
                $model->setData($key, $value);
            }
            // Clear the stale 'stores' key so getStores() reads the submitted 'store_id'.
            $model->unsetData('stores');

            $this->_eventManager->dispatch(
                'testimonial_prepare_save',
                ['testimonial' => $model, 'request' => $this->getRequest()]
            );

            $imageName = $model->getImage() ?? '';
            if (isset($data['image']) && is_array($data['image'])) {
                $imageName = $data['image'][0]['name'] ?? '';
                if (isset($data['image'][0]['tmp_name'])) {
                    try {
                        $this->imageUploader->moveFileFromTmp($imageName, true);
                    } catch (\Exception $e) {
                        $this->logger->warning(
                            'Testimonials: could not move image from tmp: ' . $e->getMessage()
                        );
                        $this->messageManager->addWarningMessage(
                            __('The testimonial image could not be saved: %1', $e->getMessage())
                        );
                        $imageName = $model->getImage() ?? '';
                    }
                }
            }
            $model->setImage($imageName);

            try {
                if (!$this->emailAddressValidator->isValid($data['email'])) {
                    throw new LocalizedException(__('Please enter a valid email address.'));
                }

                $this->testimonialRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Testimonial has been saved.'));
                $this->dataPersistor->clear('testimonial_data');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['testimonial_id' => $model->getId(), '_current' => true]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addExceptionMessage(
                    $e, __('Something went wrong while saving the testimonial.')
                );
            }

            return $resultRedirect->setPath(
                '*/*/edit',
                ['testimonial_id' => $this->getRequest()->getParam('testimonial_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
