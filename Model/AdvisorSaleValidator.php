<?php
declare(strict_types=1);

namespace GDMexico\AdvisorSale\Model;

use Magento\Catalog\Api\Data\ProductInterface;

class AdvisorSaleValidator
{
    public const ATTRIBUTE_CODE = 'advisor_only_sale';

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
            return (bool) $product->isSalable();
        }

        return false;
    }
}
