# GDMexico_AdvisorSale

Módulo Magento con código fuente compatible con PHP 7.4+ (validado para el proyecto Magento 2.4.9) que implementa la venta de productos de Remate exclusivamente mediante asesor Tawk.to.

## Qué hace

- Crea el atributo de producto `advisor_only_sale` (Sí/No), agregado a todos los attribute sets existentes.
- Producto normal (`0`): Magento conserva su comportamiento actual.
- Producto asesor (`1`) y salable según la lógica de disponibilidad de Magento: oculta Add to Cart/Buy Now en PDP y muestra **Comprar con un asesor**.
- Producto asesor (`1`) no salable según Magento: muestra **Agotado** y no inicia Tawk.to.
- El CTA usa la integración Tawk.to ya existente (`Tawk_Widget`) e intenta enviar ID, SKU, nombre, precio y URL.
- Bloquea a nivel de `Magento\Quote\Model\Quote::addProduct` cualquier intento de incorporar un producto de asesor al quote. Esto cubre storefront, REST/GraphQL estándar y el endpoint custom `GDMexico_BuyNow` del proyecto, ya que termina usando el quote/cart de Magento.
- No crea pedidos, links de pago, reservas ni cobros.

## Instalación

Copiar la carpeta como:

```bash
app/code/GDMexico/AdvisorSale
```

Ejecutar:

```bash
php bin/magento module:enable GDMexico_AdvisorSale
php bin/magento setup:upgrade
php bin/magento cache:flush
```

En producción:

```bash
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f es_MX
php bin/magento cache:flush
```

## Configuración de producto

Admin > Catalog > Products > producto > atributo **Venta únicamente mediante asesor**.

- No = venta Magento normal.
- Sí = venta únicamente mediante asesor.

Para mantener productos agotados visibles, Magento debe tener habilitada la configuración de catálogo correspondiente a mostrar productos sin stock. El módulo no fuerza esa configuración global porque afectaría a todo el catálogo.

## Validaciones recomendadas

1. Normal + salable: Add to Cart funciona.
2. Normal + no salable: comportamiento estándar Magento.
3. Asesor + salable: precio visible, sin Add to Cart, CTA visible y abre Tawk.to.
4. Asesor + no salable: precio visible, muestra Agotado, sin CTA.
5. POST directo de Add to Cart con producto asesor: rechazado.
6. Endpoint `gdmexbuy` con producto asesor: rechazado.
7. REST `V1/carts/*/items`: rechazado.
8. GraphQL `addProductsToCart`: rechazado.
9. Wishlist/Compare: no se modifican.
10. Confirmar que no se crea reserva MSI por pulsar el CTA.

## Nota de Tawk.to

El proyecto incluye el módulo `Tawk_Widget`. El JS acepta tanto `window.Tawk_API` como la variable legacy `window.$_Tawk_API` observada en esa integración. El envío de contexto usa `addEvent`/`setAttributes` cuando esas funciones están disponibles y después maximiza el chat.
