<?php

namespace Swissup\Testimonials\Installer\Command;

use Swissup\Testimonials\Model\Data;

class Testimonials
{
    private $logger;

    private \Swissup\Testimonials\Model\DataFactory $testimonialFactory;

    private \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $collectionFactory;

    public function __construct(
        \Swissup\Testimonials\Model\DataFactory $testimonialFactory,
        \Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory $collectionFactory
    ) {
        $this->testimonialFactory = $testimonialFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create new testimonials if table is empty
     *
     * @param \Swissup\Marketplace\Model\Installer\Request $request
     */
    public function execute($request)
    {
        $this->logger->info('Testimonials: Create testimonials');

        $collection = $this->collectionFactory->create()
            ->addStatusFilter(Data::STATUS_ENABLED)
            ->addWidgetFilter(1)
            ->setPageSize(1);

        if ($collection->count()) {
            return;
        }

        $defaults = [
            'status' => Data::STATUS_ENABLED,
            'store_id' => 0,
            'rating' => 5,
            'widget' => 1,
        ];

        foreach ($request->getParams() as $data) {
            $testimonial = $this->testimonialFactory->create();

            try {
                $testimonial->setData(array_merge($defaults, $data))->save();
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage());
                continue;
            }
        }
    }
}
