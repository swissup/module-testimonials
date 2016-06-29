<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

use Swissup\Testimonials\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassDisable
 */
class MassDisable extends AbstractMassStatus
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
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Swissup\Testimonials\Model\ResourceModel\Data\Collection';

    /**
     * Testimonials model
     *
     * @var string
     */
    protected $model = 'Swissup\Testimonials\Model\Data';

    /**
     * Testimonial disable status
     *
     * @var int
     */
    protected $status = 3;
}