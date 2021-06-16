<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

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
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Swissup\Testimonials\ImageUpload $imageUploader
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->imageUploader = $imageUploader;
        $this->testimonialsFactory = $testimonialsFactory;
        $this->dateFilter = $dateFilter;
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
            /** @var \Swissup\Testimonials\Model\Data $model */
            $model = $this->testimonialsFactory->create();

            if (empty($data['testimonial_id'])) {
                $data['testimonial_id'] = null;
            }

            if (!empty($data['date'])) {
                $data['date'] = $this->dateFilter->filter($data['date']);
            }

            $id = $this->getRequest()->getParam('testimonial_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'testimonial_prepare_save',
                ['testimonial' => $model, 'request' => $this->getRequest()]
            );

            $imageName = '';
            if (isset($data["image"]) && is_array($data["image"])) {
                $imageName = isset($data["image"][0]['name'])
                    ? $data["image"][0]['name']
                    : '';
                if (isset($data["image"][0]['tmp_name'])) {
                    try {
                        $this->imageUploader->moveFileFromTmp($imageName, true);
                    } catch (\Exception $e) { }
                }
            }
            $model->setData("image", $imageName);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('Testimonial has been saved.'));
                $this->dataPersistor->clear('testimonial_data');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['testimonial_id' => $model->getId(), '_current' => true]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException(
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
