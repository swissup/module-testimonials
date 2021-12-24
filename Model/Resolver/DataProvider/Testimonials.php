<?php
declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver\DataProvider;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\Resolver\DataProvider\Testimonial as TestimonialDataProvider;
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
     * @var int|null
     */
    private $status = null;

    /**
     * @var null|bool
     */
    private $isWidget = null;

    /**
     * @var null|bool
     */
    private $isRandomOrder = null;

    /**
     * @var string
     */
    private $orderField = DataInterface::DATE;

    /**
     * @var string
     */
    private $orderDirection = \Swissup\Testimonials\Model\ResourceModel\Data\Collection::SORT_ORDER_DESC;

    /**
     *
     * @var \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var TestimonialDataProvider
     */
    private $testimonialDataProvider;

    /**
     * @param \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $collectionFactory
     * @param TestimonialDataProvider $testimonialDataProvider
     */
    public function __construct(
        \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $collectionFactory,
        TestimonialDataProvider $testimonialDataProvider
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->testimonialDataProvider = $testimonialDataProvider;
    }

    /**
     *
     * @param array $stores
     * @return $this
     */
    public function addStoreFilter(array $stores)
    {
        $this->stores = $stores;
        return $this;
    }

    /**
     *
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = (int) $pageSize;
        return $this;
    }

    /**
     *
     * @param int $currentPage
     * @return $this
     */
    public function setCurPage($currentPage)
    {
        $this->currentPage = (int) $currentPage;
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function addWidgetFilter(bool $status = true)
    {
        $this->isWidget = (bool) $status;
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setRandomOrder(bool $status = true)
    {
        $this->isRandomOrder = (bool) $status;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->status = (int) $status;
        return $this;
    }

    /**
     * @param $field
     * @param $direction
     * @return $this
     */
    public function setOrder($field, $direction)
    {
        $this->orderField = (string) $field;
        $this->orderDirection = (string) $direction;
        return $this;
    }

    /**
     * @return \Swissup\Testimonials\Model\ResourceModel\Data\Collection
     */
    public function getCollection()
    {
        $pageSize = $this->pageSize;
        $currentPage = $this->currentPage;

        /* @var $collection \Swissup\Testimonials\Model\ResourceModel\Data\Collection */
        $collection = $this->collectionFactory->create()
            ->setCurPage($currentPage)
            ->setPageSize($pageSize)
        ;
        if ($this->status !== null) {
             $collection->addStatusFilter($this->status);
        }

        if ($this->isWidget !== null) {
            $collection->addWidgetFilter($this->isWidget ? 1 : 0);
        }

        if (count($this->stores) > 0) {
            $stores = $this->stores;
            $collection->addStoreFilter($stores);
        }

        if ($this->isRandomOrder === true) {
            $collection->setRandomOrder();
        } else {
            $collection->addOrder($this->orderField, $this->orderDirection);
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'currentPage' => $this->currentPage,
            'pageSize' => $this->pageSize,
//            \Swissup\Testimonials\Model\Data::STATUS => $this->status,
//            'isWidget' => $this->isWidget,
//            'isRandomOrder' => $this->isRandomOrder,
//            'store' => $this->stores,
        ];
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData(): array
    {
        $pageSize = $this->pageSize;
        $currentPage = $this->currentPage;

        $collection = $this->getCollection();
//        ray($collection);

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
        $data = $this->testimonialDataProvider
            ->setTestimonial($item)
            ->getData();

        return $data;
    }
}
