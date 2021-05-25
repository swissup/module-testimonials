<?php
namespace Swissup\Testimonials\Block;

use Swissup\Testimonials\Api\Data\DataInterface;
use Swissup\Testimonials\Model\Data as TestimonialsModel;

class TestimonialsForm extends \Magento\Framework\View\Element\Template
{
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    private $configHelper;

    /**
     * Get extension form helper
     * @var \Swissup\Testimonials\Helper\Form
     */
    private $formHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Swissup\Testimonials\Helper\Form $formHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Swissup\Testimonials\Helper\Form $formHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->configHelper = $configHelper;
        $this->formHelper = $formHelper;
    }

    /**
     * @return \Swissup\Testimonials\Helper\Config
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * @return \Swissup\Testimonials\Helper\Form
     */
    public function getFormHelper()
    {
        return $this->formHelper;
    }

    /**
     * Get form action
     * @return String form action url
     */
    public function getFormAction()
    {
        return $this->getUrl('testimonials/index/save');
    }

    /**
     * Get list action
     * @return String form action url
     */
    public function getListAction()
    {
        return $this->getUrl('testimonials');
    }
}
