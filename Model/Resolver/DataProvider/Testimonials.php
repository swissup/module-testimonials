<?php
declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver\DataProvider;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;
use Magento\Framework\Exception\NoSuchEntityException;

class Testimonials
{
    /**
     *
     * @var array[int]
     */
    private $stores = [];

    /**
     *
     * @var integer
     */
    private $pageSize = 20;

    /**
     *
     * @var integer
     */
    private $currentPage = 1;

    /**
     *
     * @var \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     *
     * @param array $stores
     * @return Messages
     */
    public function setStores(array $stores)
    {
        $this->stores = $stores;
        return $this;
    }

    /**
     *
     * @param int $pageSize
     * @return Messages
     */
    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     *
     * @param int $currentPage
     * @return Messages
     */
    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData(): array
    {
        $pageSize = $this->pageSize;
        $currentPage = $this->currentPage;

//        $collection = $this->collectionFactory->create()
//            ->addStatusFilter(TestimonialsModel::STATUS_ENABLED)
//        ;

//        $testimonials = $this->collectionFactory
//            ->create()
//            ->addStatusFilter(TestimonialsModel::STATUS_ENABLED)
//            ->addWidgetFilter(1)
//;
//        $testimonials->getSelect()
//            ->order(new \Zend_Db_Expr('RAND()'))
//            ->limit($this->getItemsNumber());

        $collection = $this->collectionFactory
            ->create()
            ->addStatusFilter(TestimonialsModel::STATUS_ENABLED)
            ->addOrder(
                DataInterface::DATE,
                \Swissup\Testimonials\Model\ResourceModel\Data\Collection::SORT_ORDER_DESC
            )
            ->setCurPage($currentPage)
            ->setPageSize($pageSize)
        ;

        if (count($this->stores) > 0) {
            $stores = $this->stores;
            $collection->addStoreFilter($stores);
        }

        $totalCount = $collection->getSize();
        $totalPages = ceil($totalCount / $pageSize);
        $data = [
            'total_count' => $totalCount,
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
            ]
        ];

        $items = [];
        foreach ($collection as $item) {
            $items[$item->getId()] = $this->getDataArray($item);
        }
        $data['items'] = $items;

        return $data;
    }

    /**
     *
     * @param  DataInterface $item
     * @return array
     */
    protected function getDataArray(DataInterface $item): array
    {
        $data = [
            DataInterface::TESTIMONIAL_ID => $item->getId(),
            DataInterface::STATUS         => $item->getStatus(),
            DataInterface::DATE           => $item->getDate(),
            DataInterface::NAME           => $item->getName(),
            DataInterface::EMAIL          => $item->getEmail(),
            DataInterface::MESSAGE        => $item->getMessage(),
            DataInterface::COMPANY        => $item->getCompany(),
            DataInterface::WEBSITE        => $item->getWebsite(),
            DataInterface::TWITTER        => $item->getTwitter(),
            DataInterface::FACEBOOK       => $item->getFacebook(),
            DataInterface::IMAGE          => $item->getImage(),
            DataInterface::RATING         => $item->getRating(),
            DataInterface::WIDGET         => $item->getWidget()
        ];

        return $data;
    }
}
