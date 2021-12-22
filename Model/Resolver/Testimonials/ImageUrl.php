<?php

declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver\Testimonials;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class ImageUrl implements ResolverInterface
{
    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepository,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->assetRepository = $assetRepository;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): string {
        if (!empty($value['image'])) {
            return $value['image'];
        }

        $fileId = 'Swissup_Testimonials::images/empty.svg';
//        $params = ['_secure' => $this->getRequest()->isSecure()];
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        $params = [
            'area' => 'frontend',
            'themeId' => $themeId
        ];
        $asset = $this->assetRepository->createAsset($fileId, $params);
//        var_dump($asset->getPath());
        return $asset->getUrl();
    }
}
