<?php
namespace Swissup\Testimonials\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory;

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

    const SUCCESS_MESSAGE = '';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Item status
     *
     * @var int
     */
    protected $status = 1;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $ids = $collection->getAllIds();

        if ($ids) {
            $collection->getResource()->getConnection()->update(
                $collection->getResource()->getMainTable(),
                ['status' => $this->status],
                [self::ID_FIELD . ' IN (?)' => $ids]
            );
        }

        $this->messageManager->addSuccessMessage(__(static::SUCCESS_MESSAGE, count($ids)));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath(self::REDIRECT_URL);
    }
}
