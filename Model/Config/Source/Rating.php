<?php
namespace Swissup\Testimonials\Model\Config\Source;

class Rating implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get rating options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => '-1', 'label' => __('No rating')],
            ['value' => '1',  'label' => '1 ' . __('star')],
            ['value' => '2',  'label' => '2 ' . __('stars')],
            ['value' => '3',  'label' => '3 ' . __('stars')],
            ['value' => '4',  'label' => '4 ' . __('stars')],
            ['value' => '5',  'label' => '5 ' . __('stars')],
        ];

        return $options;
    }
}
