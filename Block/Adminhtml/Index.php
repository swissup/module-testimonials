<?php
namespace Swissup\Testimonials\Block\Adminhtml;

/**
 * Adminhtml cms blocks content block
 */
class Index extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Swissup_Testimonials';
        $this->_controller = 'adminhtml_index';
        $this->_headerText = __('Testimonials');
        $this->_addButtonLabel = __('Add New Testimonial');
        parent::_construct();
    }
}
