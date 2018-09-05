<?php
namespace Swissup\Testimonials\Controller\Adminhtml\Image;

class Upload extends \Magento\Catalog\Controller\Adminhtml\Category\Image\Upload
{
    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Swissup_Testimonials::save');
    }
}
