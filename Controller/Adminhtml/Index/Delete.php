<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

use Swissup\Testimonials\Api\TestimonialRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Testimonials::delete';

    /**
     * @var TestimonialRepositoryInterface
     */
    private $testimonialRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param TestimonialRepositoryInterface $testimonialRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        TestimonialRepositoryInterface $testimonialRepository
    ) {
        $this->testimonialRepository = $testimonialRepository;
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
        $id = (int) $this->getRequest()->getParam('testimonial_id');
        if ($id) {
            try {
                $this->testimonialRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Testimonial was deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Can\'t find a testimonial to delete.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['testimonial_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('Can\'t find a testimonial to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
