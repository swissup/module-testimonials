<?php
namespace Swissup\Testimonials\Model\Config\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Swissup\Testimonials\Model\Data
     */
    protected $data;

    /**
     * Constructor
     *
     * @param \Swissup\Testimonials\Model\Data $data
     */
    public function __construct(\Swissup\Testimonials\Model\Data $data)
    {
        $this->data = $data;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->data->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}