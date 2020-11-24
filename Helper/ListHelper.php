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
    protected $ioFile;

    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    public $configHelper;

    /**
     * image model
     *
     * @var \Swissup\Core\Api\Media\FileInfoInterface
     */
    protected $imageModel;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Swissup\Core\Api\Media\FileInfoInterface $imageModel
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Image\Factory $imageFactory,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Swissup\Core\Api\Media\FileInfoInterface $imageModel
    ) {
        $this->ioFile = $ioFile;
        $this->imageFactory = $imageFactory;
        $this->configHelper = $configHelper;
        $this->imageModel = $imageModel;
        parent::__construct($context);
    }

    /**
     * Return URL for resized image
     *
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @param int $imgWidth
     * @param int $imgHeight
     * @return bool|string
     */
    public function resize(
        \Swissup\Testimonials\Model\Data $testimonial,
        $imgWidth = 0,
        $imgHeight = 0
    ) {
        if (!$this->getImagePath($testimonial)) {
            return false;
        }

        $width = $imgWidth ?: $this->configHelper->getImageWidth();
        $height = $imgHeight ?: $this->configHelper->getImageHeight() ?: null;
        $imageFile = $this->getImagePath($testimonial);
        $cacheDir  = $this->getBaseDir() . '/' . 'cache' . '/' . $width;
        $cacheUrl  = $this->getBaseUrl() . '/' . 'cache' . '/' . $width . '/';

        $io = $this->ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(['path' => $cacheDir]);
        if ($io->fileExists($cacheDir . '/' . $imageFile)) {
            return $cacheUrl . $imageFile;
        }

        try {
            $image = $this->imageFactory->create($this->getBaseDir() . '/' . $imageFile);
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
        return $this->imageModel->getBaseDir();
    }

    /**
     * Return the Base URL for News Item images
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->imageModel->getBaseUrl();
    }

    /**
     * Get profile image path or placeholder
     * @param \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getImagePath($testimonial)
    {
        $image = $testimonial->getImage();
        if (!$image && $placeholderImage = $this->configHelper->getPlaceholderImage()) {
            $image = $placeholderImage;
        }

        return $image;
    }

    /**
     * Get rating value in percents
     * @param  \Swissup\Testimonials\Model\Data $testimonial
     * @return String
     */
    public function getRatingPercent($testimonial)
    {
        $ratingPercent = $testimonial->getRating() / 5 * 100;

        return (String)$ratingPercent;
    }
}
