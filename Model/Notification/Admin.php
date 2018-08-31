<?php
namespace Swissup\Testimonials\Model\Notification;

class Admin implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Get extension configuration helper
     * @var \Swissup\Testimonials\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Swissup\Testimonials\Helper\Config $configHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Swissup\Testimonials\Helper\Config $configHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->configHelper = $configHelper;
        $this->logger = $logger;
    }

    protected function sendEmail(
        $from, $to, $templateId, $vars, $store, $area = \Magento\Framework\App\Area::AREA_FRONTEND
    ) {
        $this->inlineTranslation->suspend();
        $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions([
                'area' => $area,
                'store' => $store->getId()
            ])
            ->setTemplateVars($vars)
            ->setFrom($from)
            ->addTo($to['email'], $to['name']);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getItem();
        if ($item->getId() == null && $this->configHelper->isAdminNotificationEnabled()) {
            $store = $this->storeManager->getStore($item->getStoreId());
            $from = $this->configHelper->getAdminNotificationSendFrom();
            $to = [
                'email' => $this->configHelper->getAdminEmail(),
                'name' => 'Store Administrator'
            ];
            $templateId = $this->configHelper->getAdminEmailTemplate();
            $subject = $this->configHelper->getAdminEmailSubject();
            $image = $item->getImage() ? __("Yes") : __("No");
            $statuses = $item->getAvailableStatuses();
            $status = $statuses[$item->getStatus()];
            $vars = [
                'admin_subject' => $subject,
                'user_name' => $item->getName(),
                'user_email' => $item->getEmail(),
                'message' => $item->getMessage(),
                'company' => $item->getCompany(),
                'website' => $item->getWebsite(),
                'facebook' => $item->getFacebook(),
                'twitter' => $item->getTwitter(),
                'rating' => $item->getRating(),
                'image' =>  $image,
                'status' => $status,
                'store_view' => $store->getFrontendName()
            ];

            try {
                $this->sendEmail($from, $to, $templateId, $vars, $store);
            } catch (\Magento\Framework\Exception\MailException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $this;
    }
}
