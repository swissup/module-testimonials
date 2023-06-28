<?php
declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Swissup\Testimonials\Model\Resolver\DataProvider\Testimonial as DataProvider;

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
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Swissup\Testimonials\Model\DataFactory $testimonialsFactory
     * @param DataProvider $dataProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Swissup\Testimonials\Model\DataFactory $testimonialsFactory,
        DataProvider $dataProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->testimonialsFactory = $testimonialsFactory;
        $this->dataProvider = $dataProvider;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {

        if (empty($args['email']) || !is_string($args['email'])) {
            throw new GraphQlInputException(__('"email" value should be specified'));
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
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function execute(array $postData)
    {
        $customer = $this->getCustomer($postData['email']);
        $postData['name'] = $customer->getId() ?
            $customer->getFirstName() . ' ' . $customer->getLastName() : $postData['name'];
        $postData['email'] = $customer->getId() ? $customer->getEmail() : $postData['email'];
        $postData['store_id'] = (int) $this->storeManager->getStore()->getId();

        $postData['status'] = $this->configHelper->isAutoApprove() ?
            \Swissup\Testimonials\Model\Data::STATUS_ENABLED :
            \Swissup\Testimonials\Model\Data::STATUS_AWAITING_APPROVAL;
        $postData['widget'] = 1;

        $testimonial = $this->testimonialsFactory->create();
        $testimonial->setData($postData);
        $testimonial->save();

        return $testimonial;
    }

    /**
     * @param $email
     * @return \Magento\Customer\Api\Data\CustomerInterface|\Magento\Customer\Model\Customer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomer($email)
    {
        $isLoggedIn = $this->customerSession->isLoggedIn();
        $customerSession = $this->customerSession->getCustomer();
        $customer = $customerSession;
        if (!$isLoggedIn) {
            try {
                $customer = $this->customerRepository->get($email);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $customer = $customerSession;
            }
        }

        return $customer;
    }
}
