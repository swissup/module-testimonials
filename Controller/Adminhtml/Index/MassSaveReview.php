<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class MassSaveReview extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Testimonials::save';

    /**
     * Email for guests
     */
    const GUEST_EMAIL = 'guest@example.com';

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    private $reviewFactory;

    /**
     * Rating model
     *
     * @var \Magento\Review\Model\RatingFactory
     */
    private $ratingFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    private $testimonialsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->customerRepository = $customerRepository;
        $this->testimonialsFactory = $testimonialsFactory;
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
        $reviewsIds = $this->getRequest()->getParam('reviews');
        if (!is_array($reviewsIds)) {
            $this->messageManager->addError(__('Please select review(s).'));
        } else {
            try {
                foreach ($reviewsIds as $reviewId) {
                    $review = $this->reviewFactory->create()->load($reviewId);
                    $this->saveReview($review);
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 testimonial(s) have been created.', count($reviewsIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e, __('Something went wrong while exporting these records.')
                );
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }

    /**
     * Save review to testimonial
     * @param  \Magento\Review\Model\Review $review
     * @return void
     */
    protected function saveReview($review)
    {
        $model = $this->testimonialsFactory->create();

        if ($customerId = $review->getCustomerId()) {
            $customerEmail = $this->customerRepository
                ->getById($customerId)
                ->getEmail();
        } else {
            $customerEmail = self::GUEST_EMAIL;
        }

        $rating = -1;
        $ratingSummary = $this->ratingFactory->create()
            ->getReviewSummary($review->getId());
        if ($ratingSummary->getCount()) {
            $rating = ceil($ratingSummary->getSum() / $ratingSummary->getCount());
            $rating = round(5 * ($rating / 100));
        }

        $model->setName($review->getNickname());
        $model->setMessage($review->getDetail());
        $model->setStoreId($review->getStoreId());
        $model->setDate($review->getCreatedAt());
        $model->setEmail($customerEmail);
        $model->setRating($rating);

        $model->save();
    }
}
