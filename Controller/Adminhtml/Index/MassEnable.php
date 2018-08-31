<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Index;

use Swissup\Testimonials\Model\Data as TestimonialsModel;

/**
 * Class MassEnable
 */
class MassEnable extends \Swissup\Testimonials\Controller\Adminhtml\AbstractMassStatus
{
    /**
     * Testimonial enable status
     *
     * @var int
     */
    protected $status = TestimonialsModel::STATUS_ENABLED;
}
