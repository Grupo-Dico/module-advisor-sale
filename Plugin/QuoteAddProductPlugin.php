<?php
declare(strict_types=1);

namespace GDMexico\AdvisorSale\Plugin;

use GDMexico\AdvisorSale\Model\AdvisorSaleValidator;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

class QuoteAddProductPlugin
{
    /** @var AdvisorSaleValidator */
    private $advisorSaleValidator;

    public function __construct(AdvisorSaleValidator $advisorSaleValidator)
    {
        $this->advisorSaleValidator = $advisorSaleValidator;
    }

    /**
     * Blocks advisor-only products before they can enter a quote.
     *
     * @param Quote $subject
     * @param Product $product
     * @param mixed $request
     * @param string|null $processMode
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct(
        Quote $subject,
        Product $product,
        $request = null,
        $processMode = null
    ): array {
        if ($this->advisorSaleValidator->isAdvisorOnly($product)) {
            throw new LocalizedException(
                __('Este producto se vende únicamente mediante un asesor y no puede agregarse al carrito.')
            );
        }

        return [$product, $request, $processMode];
    }
}
