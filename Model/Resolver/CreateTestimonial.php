<?php
declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Swissup\Testimonials\Api\TestimonialRepositoryInterface;
use Swissup\Testimonials\Model\Resolver\DataProvider\Testimonial as DataProvider;
use Magento\Framework\Validator\EmailAddress as EmailAddressValidator;

class CreateTestimonial implements ResolverInterface
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var \Swissup\Testimonials\Model\DataFactory
     */
    private $testimonialsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var EmailAddressValidator
     */
    private $emailAddressValidator;

    /**
     * @var TestimonialRepositoryInterface
     */
    private $testimonialRepository;

    /**
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     * @param DataProvider $dataProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param EmailAddressValidator $emailAddressValidator
     * @param TestimonialRepositoryInterface $testimonialRepository
     */
    public function __construct(
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory,
        DataProvider $dataProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        EmailAddressValidator $emailAddressValidator,
        TestimonialRepositoryInterface $testimonialRepository
    ) {
        $this->testimonialsFactory = $testimonialsFactory;
        $this->dataProvider = $dataProvider;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->customerSession = $customerSession;
        $this->emailAddressValidator = $emailAddressValidator;
        $this->testimonialRepository = $testimonialRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {

        if (empty($args['email']) || !is_string($args['email'])) {
            throw new GraphQlInputException(__('"email" value should be specified'));
        }

        if (!$this->emailAddressValidator->isValid($args['email'])) {
            throw new GraphQlInputException(__('Please enter a valid email address.'));
        }

        if (empty($args['name']) || !is_string($args['name'])) {
            throw new GraphQlInputException(__('"name" value should be specified'));
        }

        if (empty($args['message']) || !is_string($args['message'])) {
            throw new GraphQlInputException(__('"message" value should be specified'));
        }

        if (empty($args['rating']) || !is_integer($args['rating'])) {
            throw new GraphQlInputException(__('"rating" value should be specified'));
        }

        $configHelper = $this->configHelper;
        $postData = [
            'name' => (string) $args['name'],
            'email' => (string) $args['email'],
            'message' => (string) $args['message'],
            'rating' => (int) $args['rating'],
            'company' => (string) $configHelper->isCompanyEnabled() && isset($args['company']) ? $args['company'] : '',
            'website' => (string) $configHelper->isWebsiteEnabled() && isset($args['website']) ? $args['website'] : '',
            'twitter' => (string) $configHelper->isTwitterEnabled() && isset($args['twitter']) ? $args['twitter'] : '',
            'facebook' => (string) $configHelper->isFacebookEnabled() && isset($args['facebook']) ? $args['facebook'] : '',
        ];

        $testimonial = $this->execute($postData);

        $data = $this->dataProvider
            ->setTestimonial($testimonial)
            ->getData();

        return $data;
    }

    /**
     * @param array $postData
     * @return \Swissup\Testimonials\Api\Data\DataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function execute(array $postData)
    {
        $customer = $this->getLoggedInCustomer();
        if ($customer !== null) {
            $postData['name'] = $customer->getFirstName() . ' ' . $customer->getLastName();
            $postData['email'] = $customer->getEmail();
        }
        $postData['store_id'] = (int) $this->storeManager->getStore()->getId();

        $postData['status'] = $this->configHelper->isAutoApprove() ?
            \Swissup\Testimonials\Model\Data::STATUS_ENABLED :
            \Swissup\Testimonials\Model\Data::STATUS_AWAITING_APPROVAL;
        $postData['widget'] = 1;

        $testimonial = $this->testimonialsFactory->create();
        $testimonial->setData($postData);

        return $this->testimonialRepository->save($testimonial);
    }

    /**
     * Returns the currently logged-in customer, or null for guests.
     *
     * Guest emails are never silently matched to a registered customer account;
     * the submitter's own name is preserved as entered.
     *
     * @return \Magento\Customer\Model\Customer|null
     */
    private function getLoggedInCustomer(): ?\Magento\Customer\Model\Customer
    {
        if (!$this->customerSession->isLoggedIn()) {
            return null;
        }

        $customer = $this->customerSession->getCustomer();

        return $customer->getId() ? $customer : null;
    }
}
