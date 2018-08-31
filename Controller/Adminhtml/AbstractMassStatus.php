<?php
namespace Swissup\Testimonials\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class AbstractMassStatus
 */
class AbstractMassStatus extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Testimonials::approve';

    /**
     * Field id
     */
    const ID_FIELD = 'testimonial_id';

    /**
     * Redirect url
     */
    const REDIRECT_URL = '*/*/';

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    protected $testimonialsFactory;

    /**
     * @var \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory
     */
    protected $testimonialsCollectionFactory;

    /**
     * Item status
     *
     * @var int
     */
    protected $status = 1;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $collection
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory,
        \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $testimonialsCollectionFactory
    ) {
        parent::__construct($context);
        $this->testimonialsFactory = $testimonialsFactory;
        $this->testimonialsCollectionFactory = $testimonialsCollectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $success = true;
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');
        try {
            if (isset($excluded)) {
                if (!empty($excluded) && $excluded != "false") {
                    $this->excludedSetStatus($excluded);
                } else {
                    $this->setStatusAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedSetStatus($selected);
            } else {
                $success = false;
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $success = false;
            $this->messageManager->addError($e->getMessage());
        }

        if ($success) {
            $this->messageManager->addSuccess(__('Testimonial(s) status was changed successfully'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    /**
     * Set status to all
     *
     * @return void
     * @throws \Exception
     */
    protected function setStatusAll()
    {
        $collection = $this->testimonialsCollectionFactory->create();
        $this->setStatus($collection);
    }

    /**
     * Set status to all but not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedSetStatus(array $excluded)
    {
        $collection = $this->testimonialsCollectionFactory->create();
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setStatus($collection);
    }

    /**
     * Set status to selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedSetStatus(array $selected)
    {
        $collection = $this->testimonialsCollectionFactory->create();
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setStatus($collection);
    }

    /**
     * Set status to collection items
     *
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\Collection $collection
     * @return void
     */
    protected function setStatus(
        \Swissup\Testimonials\Model\ResourceModel\Data\Collection $collection
    ) {
        foreach ($collection->getAllIds() as $id) {
            $model = $this->testimonialsFactory->create();
            $model->load($id);
            $model->setStatus($this->status);
            $model->save();
        }
    }
}
