<?php

$title = elgg_extract('title', $vars, '');
$description = elgg_extract('description', $vars, '');
$price = elgg_extract('price', $vars, '');
$currency = elgg_get_plugin_setting('currency', 'paypal_marketplace', 'USD');

// Title field
echo elgg_view_field([
    '#type' => 'text',
    '#label' => elgg_echo('paypal_marketplace:add:title'),
    'name' => 'title',
    'value' => $title,
    'required' => true,
]);

// Description field
echo elgg_view_field([
    '#type' => 'longtext',
    '#label' => elgg_echo('paypal_marketplace:add:description'),
    'name' => 'description',
    'value' => $description,
    'required' => true,
]);

// Transaction type field
echo elgg_view_field([
    '#type' => 'select',
    '#label' => elgg_echo('paypal_marketplace:transaction_type'),
    'name' => 'transaction_type',
    'value' => 'sell',
    'options_values' => [
        'buy' => elgg_echo('paypal_marketplace:transaction_type:buy'),
        'sell' => elgg_echo('paypal_marketplace:transaction_type:sell'),
        'rent' => elgg_echo('paypal_marketplace:transaction_type:rent'),
        'trade' => elgg_echo('paypal_marketplace:transaction_type:trade'),
        'auction' => elgg_echo('paypal_marketplace:transaction_type:auction'),
        'gift' => elgg_echo('paypal_marketplace:transaction_type:gift'),
        'donate' => elgg_echo('paypal_marketplace:transaction_type:donate'),
    ],
    'required' => true,
]);

// Price field
echo elgg_view_field([
    '#type' => 'number',
    '#label' => elgg_echo('paypal_marketplace:add:price', [$currency]),
    'name' => 'price',
    'value' => $price,
    'min' => 0,
    'step' => '0.01',
    'required' => true,
]);

// Currency field
echo elgg_view_field([
    '#type' => 'select',
    '#label' => elgg_echo('paypal_marketplace:currency'),
    'name' => 'currency',
    'value' => $currency,
    'options_values' => [
        'USD' => 'USD - US Dollar',
        'EUR' => 'EUR - Euro',
        'GBP' => 'GBP - British Pound',
        'CAD' => 'CAD - Canadian Dollar',
        'AUD' => 'AUD - Australian Dollar',
        'JPY' => 'JPY - Japanese Yen',
    ],
    'required' => true,
]);

// Image upload field
echo elgg_view_field([
    '#type' => 'file',
    '#label' => elgg_echo('paypal_marketplace:add:image'),
    'name' => 'images[]',
    'multiple' => true,
    'accept' => 'image/*',
]);

// Submit button
$footer = elgg_view_field([
    '#type' => 'submit',
    'value' => elgg_echo('paypal_marketplace:add:submit'),
]);

elgg_set_form_footer($footer); 