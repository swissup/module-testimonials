<?php
namespace Swissup\Testimonials\Ui\DataProvider\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Swissup\Testimonials\Model\ResourceModel\Data\Collection;
use Swissup\Testimonials\Model\ResourceModel\Data\CollectionFactory;

class TestimonialDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Swissup\Core\Api\Media\FileInfoInterface
     */
    protected $fileInfo;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param \Swissup\Core\Api\Media\FileInfoInterface $fileInfo
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        \Swissup\Core\Api\Media\FileInfoInterface $fileInfo,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->fileInfo = $fileInfo;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        /** @var \Swissup\Testimonials\Model\Data $testimonial */
        foreach ($items as $testimonial) {
            $data = $testimonial->getData();

            // prepare image data for ui element
            $image = $testimonial->getData('image');
            if ($image && is_string($image)) {
                $data['image'] = [ $this->fileInfo->getImageData($image) ];
            }

            $this->loadedData[$testimonial->getId()] = $data;
        }

        $data = $this->dataPersistor->get('testimonial_data');
        if (!empty($data)) {
            // update image data, if image was just uploaded
            if (isset($data['image'])
                && is_array($data['image'])
                && isset($data['image'][0]['name'])
                && isset($data['image'][0]['tmp_name'])) {

                $data['image'] = [
                    $this->fileInfo->getImageData($data['image'][0]['name'])
                ];
            }

            $testimonial = $this->collection->getNewEmptyItem();
            $testimonial->setData($data);
            $this->loadedData[$testimonial->getId()] = $testimonial->getData();
            $this->dataPersistor->clear('testimonial_data');
        }

        return $this->loadedData;
    }
}
