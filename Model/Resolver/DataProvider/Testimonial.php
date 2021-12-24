<?php
declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver\DataProvider;

use Swissup\Testimonials\Api\Data\DataInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Testimonial
{
    /**
     *
     * @var DataInterface
     */
    private $testimonial;

    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    private $configHelper;

    /**
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     */
    public function __construct(
        \Swissup\Testimonials\Helper\Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     *
     * @param DataInterface $testimonial
     * @return Message
     */
    public function setTestimonial(DataInterface $testimonial)
    {
        $this->testimonial = $testimonial;
        return $this;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData(): array
    {
        $data = [];
        if ($this->testimonial) {
            $data = $this->getDataArray($this->testimonial);
        }

        return $data;
    }

    /**
     *
     * @param  DataInterface $item
     * @return array
     */
    protected function getDataArray(DataInterface $item): array
    {
        $configHelper = $this->configHelper;
        $data = [
            DataInterface::TESTIMONIAL_ID => $item->getId(),
            DataInterface::STATUS         => $item->getStatus(),
            DataInterface::DATE           => $item->getDate(),
            DataInterface::NAME           => $item->getName(),
            DataInterface::EMAIL          => $configHelper->showUserEmail() ? $item->getEmail() : null,
            DataInterface::MESSAGE        => $item->getMessage(),
            DataInterface::COMPANY        => $configHelper->isCompanyEnabled() ? $item->getCompany() : null,
            DataInterface::WEBSITE        => $configHelper->isWebsiteEnabled() ? $item->getWebsite() : null,
            DataInterface::TWITTER        => $configHelper->isTwitterEnabled() ? $item->getTwitter() : null,
            DataInterface::FACEBOOK       => $configHelper->isFacebookEnabled() ? $item->getFacebook() : null,
            DataInterface::IMAGE          => $item->getImage(),
            DataInterface::RATING         => $item->getRating(),
            DataInterface::WIDGET         => $item->getWidget()
        ];

        return $data;
    }
}
