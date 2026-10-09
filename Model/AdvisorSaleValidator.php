<?php
declare(strict_types=1);

namespace GDMexico\AdvisorSale\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\Config as EavConfig;

class AdvisorSaleValidator
{
    public const ATTRIBUTE_CODE = 'advisor_only_sale';
    private const STATUS_ATTRIBUTE = 'status_stock';

    /** @var EavConfig */
    private $eavConfig;

    public function __construct(EavConfig $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    public function isMarkedOutOfStock(ProductInterface $product): bool
    {
        $value = $product->getCustomAttribute(self::STATUS_ATTRIBUTE);
        $optionId = $value ? $value->getValue() : null;
        if (($optionId === null || $optionId === '') && method_exists($product, 'getData')) {
            $optionId = $product->getData(self::STATUS_ATTRIBUTE);
        }
        if ($optionId === null || $optionId === '') {
            return false;
        }
        $attribute = $this->eavConfig->getAttribute('catalog_product', self::STATUS_ATTRIBUTE);
        if (!$attribute || !$attribute->getId()) {
            return false;
        }
        $label = $attribute->getSource()->getOptionText($optionId);
        return is_string($label) && in_array(mb_strtolower(trim($label), 'UTF-8'), ['agotado', 'out of stock'], true);
    }


    public function isAdvisorOnly(ProductInterface $product): bool
    {
        $value = null;
        $attribute = $product->getCustomAttribute(self::ATTRIBUTE_CODE);

        if ($attribute !== null) {
            $value = $attribute->getValue();
        }

        if ($value === null && method_exists($product, 'getData')) {
            $value = $product->getData(self::ATTRIBUTE_CODE);
        }

        return (bool) $value;
    }

    public function isSalable(ProductInterface $product): bool
    {
        /*
         * Magento's product salability delegates to the active inventory
         * implementation. This avoids coupling the module to MSI resolver
         * interfaces that differ between Magento installations.
         */
        if (method_exists($product, 'isSalable')) {
            return !$this->isMarkedOutOfStock($product) && (bool) $product->isSalable();
        }

        return false;
    }
}
