<?php

declare(strict_types=1);

namespace Swissup\Testimonials\Model\Resolver\Testimonials;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class DateFormat implements ResolverInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate)
    {
        $this->localeDate = $localeDate;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): string {
        return $this->localeDate->formatDateTime(
            $value['date'],
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            null,
            null
        );
    }
}
