<?php
declare(strict_types=1);
namespace GDMexico\AdvisorSale\Observer;

use GDMexico\AdvisorSale\Model\AdvisorSaleValidator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;

class ApplyAdvisorLayout implements ObserverInterface
{
    /** @var Registry */
    private $registry;
    /** @var AdvisorSaleValidator */
    private $validator;

    public function __construct(Registry $registry, AdvisorSaleValidator $validator)
    {
        $this->registry = $registry;
        $this->validator = $validator;
    }

    public function execute(Observer $observer)
    {
        $layout = $observer->getData('layout');
        if (!$layout || !in_array('catalog_product_view', $layout->getUpdate()->getHandles(), true)) {
            return;
        }
        $product = $this->registry->registry('current_product');
        if ($product instanceof Product && $this->validator->isAdvisorOnly($product)) {
            $layout->getUpdate()->addHandle('gdmexico_advisor_sale_product');
        }
    }
}
