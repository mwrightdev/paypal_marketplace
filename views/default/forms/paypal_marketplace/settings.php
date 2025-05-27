<?php

$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
    '#type' => 'text',
    '#label' => elgg_echo('paypal_marketplace:settings:paypal_client_id'),
    '#help' => elgg_echo('paypal_marketplace:settings:paypal_client_id:help'),
    'name' => 'params[paypal_client_id]',
    'value' => $plugin->paypal_client_id,
    'required' => true,
]);

echo elgg_view_field([
    '#type' => 'text',
    '#label' => elgg_echo('paypal_marketplace:settings:paypal_client_secret'),
    '#help' => elgg_echo('paypal_marketplace:settings:paypal_client_secret:help'),
    'name' => 'params[paypal_client_secret]',
    'value' => $plugin->paypal_client_secret,
    'required' => true,
]);

echo elgg_view_field([
    '#type' => 'checkbox',
    '#label' => elgg_echo('paypal_marketplace:settings:paypal_sandbox'),
    '#help' => elgg_echo('paypal_marketplace:settings:paypal_sandbox:help'),
    'name' => 'params[paypal_sandbox]',
    'value' => 'yes',
    'checked' => $plugin->paypal_sandbox === 'yes',
]);

echo elgg_view_field([
    '#type' => 'select',
    '#label' => elgg_echo('paypal_marketplace:settings:currency'),
    '#help' => elgg_echo('paypal_marketplace:settings:currency:help'),
    'name' => 'params[currency]',
    'value' => $plugin->currency ?: 'USD',
    'options_values' => [
        'USD' => 'USD - US Dollar',
        'EUR' => 'EUR - Euro',
        'GBP' => 'GBP - British Pound',
        'CAD' => 'CAD - Canadian Dollar',
        'AUD' => 'AUD - Australian Dollar',
        'JPY' => 'JPY - Japanese Yen',
    ],
]);

echo elgg_view_field([
    '#type' => 'number',
    '#label' => elgg_echo('paypal_marketplace:settings:fee_percentage'),
    '#help' => elgg_echo('paypal_marketplace:settings:fee_percentage:help'),
    'name' => 'params[fee_percentage]',
    'value' => $plugin->fee_percentage ?: 0,
    'min' => 0,
    'max' => 100,
    'step' => 0.1,
]);

$footer = elgg_view_field([
    '#type' => 'submit',
    'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer); 