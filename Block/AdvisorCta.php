<?php
declare(strict_types=1);

namespace GDMexico\AdvisorSale\Block;

use GDMexico\AdvisorSale\Model\AdvisorSaleValidator;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class AdvisorCta extends Template
{
    /** @var Registry */
    private $registry;

    /** @var AdvisorSaleValidator */
    private $advisorSaleValidator;

    /** @var PriceCurrencyInterface */
    private $priceCurrency;

    public function __construct(
        Context $context,
        Registry $registry,
        AdvisorSaleValidator $advisorSaleValidator,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->advisorSaleValidator = $advisorSaleValidator;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    public function getProduct(): ?Product
    {
        $product = $this->registry->registry('current_product');
        return $product instanceof Product ? $product : null;
    }

    public function shouldRender(): bool
    {
        $product = $this->getProduct();
        return $product !== null && $this->advisorSaleValidator->isAdvisorOnly($product);
    }

    public function isSalable(): bool
    {
        $product = $this->getProduct();
        return $product !== null && $this->advisorSaleValidator->isSalable($product);
    }

    public function getProductContext(): array
    {
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }

        return [
            'id' => (int) $product->getId(),
            'sku' => (string) $product->getSku(),
            'name' => (string) $product->getName(),
            'price' => (float) $product->getFinalPrice(),
            'price_formatted' => $this->priceCurrency->format((float) $product->getFinalPrice(), false),
            'url' => (string) $product->getProductUrl()
        ];
    }
}
