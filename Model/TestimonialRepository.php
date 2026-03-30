<?php
namespace Swissup\Testimonials\Model;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Api\TestimonialRepositoryInterface;
use Swissup\Testimonials\Model\DataFactory as TestimonialFactory;
use Swissup\Testimonials\Model\ResourceModel\Data as TestimonialResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class TestimonialRepository implements TestimonialRepositoryInterface
{
    /**
     * @var TestimonialFactory
     */
    private $testimonialFactory;

    /**
     * @var TestimonialResource
     */
    private $resource;

    /**
     * @param TestimonialFactory $testimonialFactory
     * @param TestimonialResource $resource
     */
    public function __construct(
        TestimonialFactory $testimonialFactory,
        TestimonialResource $resource
    ) {
        $this->testimonialFactory = $testimonialFactory;
        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function save(DataInterface $testimonial): DataInterface
    {
        try {
            $this->resource->save($testimonial);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save testimonial: %1', $e->getMessage()), $e);
        }

        return $testimonial;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $testimonialId): DataInterface
    {
        $testimonial = $this->testimonialFactory->create();
        $this->resource->load($testimonial, $testimonialId);

        if (!$testimonial->getId()) {
            throw new NoSuchEntityException(__('Testimonial with ID "%1" does not exist.', $testimonialId));
        }

        return $testimonial;
    }

    /**
     * @inheritdoc
     */
    public function delete(DataInterface $testimonial): bool
    {
        try {
            $this->resource->delete($testimonial);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete testimonial: %1', $e->getMessage()), $e);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $testimonialId): bool
    {
        return $this->delete($this->getById($testimonialId));
    }
}
