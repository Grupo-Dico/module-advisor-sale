define([], function () {
    'use strict';

    function getTawkApi() {
        return window.Tawk_API || window.$_Tawk_API || null;
    }

    function openChat(product, attempt) {
        var api = getTawkApi(), payload;
        attempt = attempt || 0;

        if (!api && attempt < 20) {
            window.setTimeout(function () { openChat(product, attempt + 1); }, 250);
            return;
        }
        if (!api) {
            return;
        }

        payload = {
            product_id: String(product.id || ''),
            sku: String(product.sku || ''),
            name: String(product.name || ''),
            price: String(product.price_formatted || product.price || ''),
            url: String(product.url || window.location.href)
        };

        try {
            if (typeof api.addEvent === 'function') {
                api.addEvent('advisor_product', payload, function () {});
            }
        } catch (e) {}

        try {
            if (typeof api.setAttributes === 'function') {
                api.setAttributes({
                    advisor_product_sku: payload.sku,
                    advisor_product_name: payload.name,
                    advisor_product_url: payload.url
                }, function () {});
            }
        } catch (e) {}

        if (typeof api.maximize === 'function') {
            api.maximize();
        } else if (typeof api.toggle === 'function') {
            api.toggle();
        }
    }

    return function (config) {
        var addToCartBox, addToCartButton, advisorButton;
        if (!config || !config.enabled) {
            return;
        }

        // UI solamente. El bloqueo real está en Quote::addProduct.
        addToCartButton = document.getElementById('product-addtocart-button');
        if (addToCartButton) {
            addToCartBox = addToCartButton.closest('.box-tocart');
            if (addToCartBox) {
                addToCartBox.style.display = 'none';
            } else {
                addToCartButton.style.display = 'none';
            }
        }

        // También elimina accesos directos de Buy Now presentes en el PDP.
        document.querySelectorAll('.buy-now, .buynow, [data-role="buy-now"], #buy-now').forEach(function (element) {
            element.style.display = 'none';
        });

        advisorButton = document.querySelector('[data-role="advisor-sale-button"]');
        if (advisorButton && config.salable) {
            advisorButton.addEventListener('click', function () {
                openChat(config.product || {}, 0);
            });
        }
    };
});
