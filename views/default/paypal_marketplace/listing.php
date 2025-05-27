<?php

$items = elgg_extract('items', $vars, []);
$filter = elgg_extract('filter', $vars, 'all');

if (empty($items)) {
    echo elgg_echo('paypal_marketplace:no_items');
    return;
}

$list_items = [];
foreach ($items as $item) {
    $owner = $item->getOwnerEntity();
    $price = $item->price;
    $currency = $item->currency ?: 'USD';
    $transaction_type = $item->transaction_type;
    
    $item_content = elgg_format_element('div', [
        'class' => 'paypal-marketplace-item-card',
    ],
        elgg_view('object/elements/summary', [
            'entity' => $item,
            'title' => elgg_format_element('h3', [
                'class' => 'paypal-marketplace-item-title',
            ], $item->title),
            'subtitle' => elgg_format_element('div', [
                'class' => 'paypal-marketplace-item-price',
            ], elgg_echo('paypal_marketplace:price', [$price, $currency])),
            'content' => elgg_format_element('div', [
                'class' => 'paypal-marketplace-item-description',
            ], elgg_get_excerpt($item->description, 200)),
            'metadata' => elgg_echo('paypal_marketplace:transaction_type:' . $transaction_type),
        ])
    );
    
    // Add action buttons based on transaction type
    $actions = [];
    if (elgg_is_logged_in()) {
        if (in_array($transaction_type, ['buy', 'sell'])) {
            $actions[] = elgg_view('output/url', [
                'text' => elgg_echo('Add to Cart'),
                'href' => "action/paypal_marketplace/cart/add?guid={$item->guid}",
                'class' => 'elgg-button elgg-button-action paypal-marketplace-add-to-cart',
                'is_action' => true,
            ]);
        }
        
        if ($owner->guid !== elgg_get_logged_in_user_guid()) {
            // Add to cart button for buy/sell items

            switch ($transaction_type) {
                case 'buy':
                case 'sell':
                    $actions[] = elgg_view('output/url', [
                        'text' => elgg_echo('paypal_marketplace:action:purchase'),
                        'href' => "paypal_marketplace/purchase/{$item->guid}",
                        'class' => 'elgg-button elgg-button-action',
                    ]);
                    break;
                    
                case 'auction':
                    $actions[] = elgg_view('output/url', [
                        'text' => elgg_echo('paypal_marketplace:action:bid'),
                        'href' => "paypal_marketplace/bid/{$item->guid}",
                        'class' => 'elgg-button elgg-button-action',
                    ]);
                    break;
                    
                case 'gift':
                case 'donate':
                    $actions[] = elgg_view('output/url', [
                        'text' => elgg_echo('paypal_marketplace:action:donate'),
                        'href' => "paypal_marketplace/donate/{$item->guid}",
                        'class' => 'elgg-button elgg-button-action',
                    ]);
                    break;
                    
                case 'trade':
                    $actions[] = elgg_view('output/url', [
                        'text' => elgg_echo('paypal_marketplace:action:trade'),
                        'href' => "paypal_marketplace/trade/{$item->guid}",
                        'class' => 'elgg-button elgg-button-action',
                    ]);
                    break;
            }
        } else {
            // Owner actions
            $actions[] = elgg_view('output/url', [
                'text' => elgg_echo('edit'),
                'href' => "paypal_marketplace/edit/{$item->guid}",
                'class' => 'elgg-button elgg-button-edit',
            ]);
            
            $actions[] = elgg_view('output/url', [
                'text' => elgg_echo('delete'),
                'href' => "action/paypal_marketplace/delete?guid={$item->guid}",
                'class' => 'elgg-button elgg-button-delete',
                'confirm' => elgg_echo('deleteconfirm'),
            ]);
        }
    }
    
    $item_content .= elgg_format_element('div', ['class' => 'paypal-marketplace-item-actions'], implode('', $actions));
    
    $list_items[] = elgg_format_element('div', [
        'class' => 'paypal-marketplace-item-wrapper',
        'data-guid' => $item->guid,
    ], $item_content);
}

echo elgg_format_element('div', [
    'class' => 'paypal-marketplace-list-container',
], elgg_format_element('div', [
    'class' => 'paypal-marketplace-list',
], implode('', $list_items)));

// Add pagination
$count_options = [
    'type' => 'object',
    'subtype' => 'paypal_marketplace_item',
];

// Apply the same filter as the main query
if ($filter !== 'all') {
    $count_options['metadata_name_value_pairs'] = [
        ['name' => 'transaction_type', 'value' => $filter],
    ];
}

echo elgg_view('navigation/pagination', [
    'base_url' => elgg_get_current_url(),
    'count' => elgg_count_entities($count_options),
    'limit' => 20,
    'offset' => get_input('offset', 0),
]);

elgg_require_css('paypal_marketplace/listing'); 