<?php
namespace Swissup\Testimonials\Helper;
/**
 * Testimonials list helper
 */
class ListHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * File Uploader factory
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_ioFile;
    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    public $_configHelper;
    /**
     * image model
     *
     * @var \Swissup\Testimonials\Model\Data\Image
     */
    protected $_imageModel;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Swissup\Testimonials\Model\Data\Image $imageModel
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Image\Factory $imageFactory,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Swissup\Testimonials\Model\Data\Image $imageModel
    ) {
        $this->_ioFile = $ioFile;
        $this->_imageFactory = $imageFactory;
        $this->_configHelper = $configHelper;
        $this->_imageModel = $imageModel;
        parent::__construct($context);
    }
    /**
     * Return URL for resized image
     *
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @return bool|string
     */
    public function resize(\Swissup\Testimonials\Model\Data $testimonial)
    {
        if (!$this->getImagePath($testimonial)) {
            return false;
        }
        $width = $this->_configHelper->getImageWidth();
        $height = $this->_configHelper->getImageHeight();
        $imageFile = $this->getImagePath($testimonial);
        $cacheDir  = $this->getBaseDir() . '/' . 'cache' . '/' . $width;
        $cacheUrl  = $this->getBaseUrl() . '/' . 'cache' . '/' . $width . '/';
        $io = $this->_ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(array('path' => $cacheDir));
        if ($io->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }
        try {
            $image = $this->_imageFactory->create($this->getBaseDir() . '/' . $imageFile);
            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageFile);
            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Return the base media directory testimonial images
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_imageModel->getBaseDir();
    }
    /**
     * Return the Base URL for News Item images
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_imageModel->getBaseUrl();
    }
    /**
     * Get profile image path or placeholder
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getImagePath($testimonial)
    {
        $image = $testimonial->getImage();
        if (!$image && $placeholderImage = $this->_configHelper->getPlaceholderImage()) {
            $image = $placeholderImage;
        }
        return $image;
    }
}