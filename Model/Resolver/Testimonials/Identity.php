<?php

declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver\Testimonials;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Identity for resolved products
 */
class Identity implements IdentityInterface
{
    /** @var string */
    private $cacheTag = \Swissup\Testimonials\Model\Data::CACHE_TAG;

    /**
     * Get ids for cache tag
     *
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        $ids = [];
        $idKey = 'testimonial_id';
        if (isset($item[$idKey])) {
            $ids[] = sprintf('%s_%s', $this->cacheTag, $item[$idKey]);
        }

        if (!empty($ids)) {
            array_unshift($ids, $this->cacheTag);
        }

        return $ids;
    }
}
