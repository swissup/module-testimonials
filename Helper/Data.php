<?php
namespace Swissup\Testimonials\Helper;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\ResourceModel\Data\Collection as TestimonialsCollection;
use Magento\Framework\App\Action\Action;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Swissup\Testimonials\Model\Data
     */
    protected $_testimonial;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Swissup\Testimonials\Model\Data $testimonial,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->_testimonial = $testimonial;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
}