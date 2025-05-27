<?php

$entity = elgg_extract('entity', $vars);
$is_edit = (bool) $entity;

$title = elgg_echo('paypal_marketplace:edit:title');
$submit_text = elgg_echo('save');

$fields = [
    [
        '#type' => 'text',
        '#label' => elgg_echo('paypal_marketplace:title'),
        'name' => 'title',
        'value' => $entity ? $entity->title : '',
        'required' => true,
    ],
    [
        '#type' => 'longtext',
        '#label' => elgg_echo('paypal_marketplace:description'),
        'name' => 'description',
        'value' => $entity ? $entity->description : '',
        'required' => true,
    ],
    [
        '#type' => 'select',
        '#label' => elgg_echo('paypal_marketplace:transaction_type'),
        'name' => 'transaction_type',
        'value' => $entity ? $entity->transaction_type : 'sell',
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
    ],
    [
        '#type' => 'number',
        '#label' => elgg_echo('paypal_marketplace:price'),
        'name' => 'price',
        'value' => $entity ? $entity->price : '',
        'min' => 0,
        'step' => 0.01,
        'required' => true,
    ],
    [
        '#type' => 'select',
        '#label' => elgg_echo('paypal_marketplace:currency'),
        'name' => 'currency',
        'value' => $entity ? $entity->currency : elgg_get_plugin_setting('currency', 'paypal_marketplace'),
        'options_values' => [
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
            'CAD' => 'CAD - Canadian Dollar',
            'AUD' => 'AUD - Australian Dollar',
            'JPY' => 'JPY - Japanese Yen',
        ],
        'required' => true,
    ],
    [
        '#type' => 'file',
        '#label' => elgg_echo('paypal_marketplace:images'),
        'name' => 'images[]',
        'multiple' => true,
        'accept' => 'image/*',
    ],
];

// Add auction-specific fields
if (!$entity || $entity->transaction_type === 'auction') {
    $fields[] = [
        '#type' => 'date',
        '#label' => elgg_echo('paypal_marketplace:auction:end_date'),
        'name' => 'auction_end_date',
        'value' => $entity ? $entity->auction_end_date : '',
        'timestamp' => true,
    ];
    
    $fields[] = [
        '#type' => 'number',
        '#label' => elgg_echo('paypal_marketplace:auction:min_bid'),
        'name' => 'auction_min_bid',
        'value' => $entity ? $entity->auction_min_bid : '',
        'min' => 0,
        'step' => 0.01,
    ];
}

// Add rent-specific fields
if (!$entity || $entity->transaction_type === 'rent') {
    $fields[] = [
        '#type' => 'select',
        '#label' => elgg_echo('paypal_marketplace:rent:period'),
        'name' => 'rent_period',
        'value' => $entity ? $entity->rent_period : 'day',
        'options_values' => [
            'hour' => elgg_echo('paypal_marketplace:rent:period:hour'),
            'day' => elgg_echo('paypal_marketplace:rent:period:day'),
            'week' => elgg_echo('paypal_marketplace:rent:period:week'),
            'month' => elgg_echo('paypal_marketplace:rent:period:month'),
        ],
    ];
}

// Add trade-specific fields
if (!$entity || $entity->transaction_type === 'trade') {
    $fields[] = [
        '#type' => 'longtext',
        '#label' => elgg_echo('paypal_marketplace:trade:description'),
        'name' => 'trade_description',
        'value' => $entity ? $entity->trade_description : '',
        'help' => elgg_echo('paypal_marketplace:trade:description:help'),
    ];
}

// Add gift/donate-specific fields
if (!$entity || in_array($entity->transaction_type, ['gift', 'donate'])) {
    $fields[] = [
        '#type' => 'checkbox',
        '#label' => elgg_echo('paypal_marketplace:anonymous'),
        'name' => 'anonymous',
        'value' => 'yes',
        'checked' => $entity ? $entity->anonymous === 'yes' : false,
    ];
}

// Add form fields
foreach ($fields as $field) {
    echo elgg_view_field($field);
}

// Add hidden fields
echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'guid',
    'value' => $entity ? $entity->guid : '',
]);

// Add submit button
$footer = elgg_view_field([
    '#type' => 'submit',
    'value' => $submit_text,
]);

elgg_set_form_footer($footer); 