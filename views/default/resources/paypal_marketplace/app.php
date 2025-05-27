<?php

elgg_gatekeeper();

$title = elgg_echo('paypal_marketplace:cart:title');

// Get user's cart
$cart = elgg_get_entities([
    'type' => 'object',
    'subtype' => 'paypal_marketplace_cart',
    'owner_guid' => elgg_get_logged_in_user_guid(),
    'limit' => 1,
])[0] ?? null;

if (!$cart) {
    $content = elgg_format_element('div', [
        'class' => 'paypal-marketplace-cart-empty',
    ], elgg_view('output/longtext', [
        'value' => elgg_echo('paypal_marketplace:cart:empty'),
        'class' => 'elgg-message elgg-message-info',
    ]));
} else {
    // Get cart items
    $cart_items = elgg_get_entities([
        'type' => 'object',
        'subtype' => 'paypal_marketplace_cart_item',
        'container_guid' => $cart->guid,
        'limit' => 0,
    ]);
    
    if (empty($cart_items)) {
        $content = elgg_format_element('div', [
            'class' => 'paypal-marketplace-cart-empty',
        ], elgg_view('output/longtext', [
            'value' => elgg_echo('paypal_marketplace:cart:empty'),
            'class' => 'elgg-message elgg-message-info',
        ]));
    } else {
        // Calculate total
        $total = 0;
        $currency = null;
        
        $items_content = [];
        foreach ($cart_items as $item) {
            $original_item = get_entity($item->item_guid);
            if (!$original_item) {
                continue;
            }
            
            $total += $item->price;
            $currency = $item->currency;
            
            // Create item card
            $item_content = elgg_format_element('div', [
                'class' => 'paypal-marketplace-cart-item-card',
            ], elgg_view('object/elements/summary', [
                'entity' => $original_item,
                'title' => elgg_format_element('h3', [
                    'class' => 'paypal-marketplace-cart-item-title',
                ], $item->title),
                'subtitle' => elgg_format_element('div', [
                    'class' => 'paypal-marketplace-cart-item-price',
                ], elgg_echo('paypal_marketplace:price', [$item->price, $item->currency])),
                'content' => elgg_format_element('div', [
                    'class' => 'paypal-marketplace-cart-item-description',
                ], elgg_get_excerpt($original_item->description, 200)),
                'metadata' => elgg_view('output/url', [
                    'text' => elgg_echo('paypal_marketplace:cart:remove'),
                    'href' => "action/paypal_marketplace/cart/remove?guid={$item->guid}",
                    'class' => 'elgg-button elgg-button-delete paypal-marketplace-cart-remove',
                    'is_action' => true,
                    'confirm' => elgg_echo('paypal_marketplace:cart:remove:confirm'),
                ]),
            ]));
            
            $items_content[] = $item_content;
        }
        
        // Cart container with items
        $content = elgg_format_element('div', [
            'class' => 'paypal-marketplace-cart-container',
        ], implode('', $items_content));
        
        // Cart summary
        $summary = elgg_format_element('div', [
            'class' => 'paypal-marketplace-cart-summary',
        ], elgg_format_element('div', [
            'class' => 'paypal-marketplace-cart-total',
        ], elgg_echo('paypal_marketplace:cart:total', [$total, $currency])));
        
        // Checkout button container
        $summary .= elgg_format_element('div', [
            'class' => 'paypal-marketplace-cart-actions',
        ], elgg_view('output/url', [
            'text' => elgg_echo('paypal_marketplace:cart:checkout'),
            'href' => 'paypal_marketplace/checkout',
            'class' => 'elgg-button elgg-button-action paypal-marketplace-checkout-button',
        ]));
        
        $content .= elgg_format_element('div', [
            'class' => 'paypal-marketplace-cart-footer',
        ], $summary);
    }
}

// Add custom CSS
elgg_require_css('paypal_marketplace/cart');

$layout = elgg_view_layout('default', [
    'title' => $title,
    'content' => $content,
    'filter' => false,
    'sidebar' => false,
]);

echo elgg_view_page($title, $layout);
