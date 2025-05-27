<?php

$filter = elgg_extract('filter', $vars, 'all');
$type = elgg_extract('type', $vars, 'all');

$tabs = [
    'all' => [
        'text' => elgg_echo('paypal_marketplace:filter:all'),
        'href' => 'paypal_marketplace/all',
        'selected' => $filter === 'all',
    ],
    'buy' => [
        'text' => elgg_echo('paypal_marketplace:filter:buy'),
        'href' => 'paypal_marketplace/all?filter=buy',
        'selected' => $filter === 'buy',
    ],
    'sell' => [
        'text' => elgg_echo('paypal_marketplace:filter:sell'),
        'href' => 'paypal_marketplace/all?filter=sell',
        'selected' => $filter === 'sell',
    ],
    'rent' => [
        'text' => elgg_echo('paypal_marketplace:filter:rent'),
        'href' => 'paypal_marketplace/all?filter=rent',
        'selected' => $filter === 'rent',
    ],
    'trade' => [
        'text' => elgg_echo('paypal_marketplace:filter:trade'),
        'href' => 'paypal_marketplace/all?filter=trade',
        'selected' => $filter === 'trade',
    ],
    'auction' => [
        'text' => elgg_echo('paypal_marketplace:filter:auction'),
        'href' => 'paypal_marketplace/all?filter=auction',
        'selected' => $filter === 'auction',
    ],
    'gift' => [
        'text' => elgg_echo('paypal_marketplace:filter:gift'),
        'href' => 'paypal_marketplace/all?filter=gift',
        'selected' => $filter === 'gift',
    ],
    'donate' => [
        'text' => elgg_echo('paypal_marketplace:filter:donate'),
        'href' => 'paypal_marketplace/all?filter=donate',
        'selected' => $filter === 'donate',
    ],
];

echo elgg_view('navigation/tabs', [
    'tabs' => $tabs,
    'class' => 'elgg-tabs-inline',
]);

// Additional filters
$filter_options = [
    'price_low' => elgg_echo('paypal_marketplace:filter:price_low'),
    'price_high' => elgg_echo('paypal_marketplace:filter:price_high'),
    'newest' => elgg_echo('paypal_marketplace:filter:newest'),
    'popular' => elgg_echo('paypal_marketplace:filter:popular'),
];

$filter_input = elgg_view('input/select', [
    'name' => 'sort',
    'options_values' => $filter_options,
    'value' => get_input('sort', 'newest'),
    'class' => 'elgg-input-select',
]);

echo elgg_view('input/form', [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'GET',
    'body' => $filter_input,
    'class' => 'elgg-form-filter',
]); 