<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Testimonials::save';

    /**
     * upload model
     *
     * @var \Swissup\Testimonials\Model\Upload
     */
    protected $uploadModel;

    /**
     * image model
     *
     * @var \Swissup\Testimonials\Model\Data\Image
     */
    protected $imageModel;

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    protected $testimonialsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Swissup\Testimonials\Model\Data\Image $imageModel
     * @param \Swissup\Testimonials\Model\Upload $uploadModel
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swissup\Testimonials\Model\Data\Image $imageModel,
        \Swissup\Testimonials\Model\Upload $uploadModel,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
    ) {
        $this->uploadModel = $uploadModel;
        $this->imageModel = $imageModel;
        $this->testimonialsFactory = $testimonialsFactory;
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Swissup\Testimonials\Model\Data $model */
            $model = $this->testimonialsFactory->create();

            $id = $this->getRequest()->getParam('testimonial_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'testimonial_prepare_save',
                ['testimonial' => $model, 'request' => $this->getRequest()]
            );

            $imageName = $this->uploadModel->uploadFileAndGetName(
                'image',
                $this->imageModel->getBaseDir(),
                $data
            );
            $model->setImage($imageName);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('Testimonial has been saved.'));
                $this->_getSession()->setFormData(false);
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
                $this->messageManager->addException(
                    $e, __('Something went wrong while saving the testimonial.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['testimonial_id' => $this->getRequest()->getParam('testimonial_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
