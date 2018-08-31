<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Testimonials::delete';

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    protected $testimonialsFactory = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
    ) {
        $this->testimonialsFactory = $testimonialsFactory;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('testimonial_id');
        if ($id) {
            try {
                $model = $this->testimonialsFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Testimonial was deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['testimonial_id' => $id]);
            }
        }
        $this->messageManager->addError(__('Can\'t find a testimonial to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
