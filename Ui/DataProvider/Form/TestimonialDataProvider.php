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
     * @var \Swissup\Testimonials\Model\Data\FileInfo
     */
    protected $fileInfo;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param \Swissup\Testimonials\Model\Data\FileInfo $fileInfo
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        \Swissup\Testimonials\Model\Data\FileInfo $fileInfo,
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
                $data['image'] = $this->prepareImageData($image);
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

                $data['image'] = $this->prepareImageData($data['image'][0]['name']);
            }

            $testimonial = $this->collection->getNewEmptyItem();
            $testimonial->setData($data);
            $this->loadedData[$testimonial->getId()] = $testimonial->getData();
            $this->dataPersistor->clear('testimonial_data');
        }

        return $this->loadedData;
    }

    private function prepareImageData($imageName)
    {
        $url  = $this->fileInfo->getBaseUrl() . '/' . ltrim($imageName, '/');
        $stat = $this->fileInfo->getStat($imageName);
        $mime = $this->fileInfo->getMimeType($imageName);

        return [
            [
                'name' => $imageName,
                'url'  => $url,
                'size' => isset($stat['size']) ? $stat['size'] : 0,
                'type' => $mime,
            ]
        ];
    }
}
