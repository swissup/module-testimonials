<?php
namespace Swissup\Testimonials\Block\Widgets;

/**
 * Class testimonials form widget
 * @package Swissup\Testimonials\Block\Widgets
 */
class Form extends \Swissup\Testimonials\Block\TestimonialsForm
     implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Default template to use for review widget
     */
    const DEFAULT_FORM_TEMPLATE = 'form.phtml';

    public function _construct()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_FORM_TEMPLATE);
        }
        return parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'captcha',
            $this->getLayout()->createBlock('Magento\Captcha\Block\Captcha')
                ->setFormId('testimonial_new')
                ->setImgWidth(230)
                ->setImgHeight(50)
        );
        return parent::_prepareLayout();
    }
}
