<?php
namespace Swissup\Testimonials\Api;

use Swissup\Testimonials\Api\Data\DataInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

interface TestimonialRepositoryInterface
{
    /**
     * Save testimonial.
     *
     * @param DataInterface $testimonial
     * @return DataInterface
     * @throws CouldNotSaveException
     */
    public function save(DataInterface $testimonial): DataInterface;

    /**
     * Retrieve testimonial by ID.
     *
     * @param int $testimonialId
     * @return DataInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $testimonialId): DataInterface;

    /**
     * Delete testimonial.
     *
     * @param DataInterface $testimonial
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(DataInterface $testimonial): bool;

    /**
     * Delete testimonial by ID.
     *
     * @param int $testimonialId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $testimonialId): bool;
}
