<?php
namespace Swissup\Testimonials\Block;

/**
 * Class New Link
 * @package Swissup\Testimonials\Block
 */
class NewLink extends \Magento\Framework\View\Element\Template
{
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Submit Your Testimonial');
    }
    /**
     * @return string
     */
    public function getLink()
    {
        return $this->getUrl('testimonials/index/new');
    }
}
