<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

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
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context,
        \Swissup\Testimonials\Model\Data\Image $imageModel,
        \Swissup\Testimonials\Model\Upload $uploadModel)
    {
        $this->uploadModel = $uploadModel;
        $this->imageModel = $imageModel;
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
            $model = $this->_objectManager->create('Swissup\Testimonials\Model\Data');

            $id = $this->getRequest()->getParam('testimonial_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'testimonial_prepare_save',
                ['testimonial' => $model, 'request' => $this->getRequest()]
            );

            $imageName = $this->uploadModel->uploadFileAndGetName('image', $this->imageModel->getBaseDir(), $data);
            $model->setImage($imageName);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('Testimonial has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['testimonial_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the testimonial.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['testimonial_id' => $this->getRequest()->getParam('testimonial_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
