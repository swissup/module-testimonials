<?php
declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Swissup\Testimonials\Model\Resolver\DataProvider\Testimonials as DataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Data resolver, used for GraphQL request processing
 */
class Testimonials implements ResolverInterface
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @param DataProvider $dataProvider
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        DataProvider $dataProvider,
        StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->dataProvider = $dataProvider;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
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

        if (isset($args['currentPage']) && $args['currentPage'] < 0) {
            throw new GraphQlInputException(__('currentPage value must be greater than -1.'));
        }
        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        $resultData = $this->getData($args);

        return $resultData;
    }

    /**
     * @param array $args
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getData(array $args): array
    {
        try {
            $provider = $this->dataProvider;

            $stores = [Store::DEFAULT_STORE_ID];
            $storeId = (int) $this->storeManager->getStore()->getId();
            $stores[] = $storeId;
            $provider->setStores($stores);

            if (isset($args['pageSize'])) {
                $provider->setPageSize((int) $args['pageSize']);
            }

            if (isset($args['currentPage'])) {
                $provider->setCurrentPage((int) $args['currentPage']);
            }

            if($this->customerSession->isLoggedIn()) {
                $customer = $this->customerSession->getCustomer();
                if ($customer && $customer->getId()) {
                    $provider->setCustomerId((int) $customer->getId());
                }
            }

            $data = $provider->getData();
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $data;
    }
}
