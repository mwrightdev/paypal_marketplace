<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\GatekeeperException;

// Ensure user is logged in
elgg_gatekeeper();

$guid = get_input('guid');
$item = get_entity($guid);

if (!$item instanceof \ElggObject || $item->getSubtype() !== 'paypal_marketplace_item') {
    throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:invalid_item'));
}

// Check if item is available for cart
if (!in_array($item->transaction_type, ['buy', 'sell'])) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:invalid_type'));
}

// Check if user is not the owner
// if ($item->owner_guid === elgg_get_logged_in_user_guid()) {
//     throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:own_item'));
// }

// Get or create cart
$cart = elgg_get_entities([
    'type' => 'object',
    'subtype' => 'paypal_marketplace_cart',
    'owner_guid' => elgg_get_logged_in_user_guid(),
    'limit' => 1,
])[0] ?? null;

if (!$cart) {
    $cart = new \ElggObject();
    $cart->setSubtype('paypal_marketplace_cart');
    $cart->owner_guid = elgg_get_logged_in_user_guid();
    $cart->container_guid = elgg_get_logged_in_user_guid();
    $cart->access_id = ACCESS_PRIVATE;
    $cart->title = elgg_echo('paypal_marketplace:app:title');
    
    if (!$cart->save()) {
        throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:save'));
    }
}

// Check if item is already in cart
$cart_items = elgg_get_entities([
    'type' => 'object',
    'subtype' => 'paypal_marketplace_cart_item',
    'container_guid' => $cart->guid,
    'metadata_name_value_pairs' => [
        ['name' => 'item_guid', 'value' => $item->guid],
    ],
    'limit' => 1,
]);

if (!empty($cart_items)) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:already_in_cart'));
}

// Add item to cart
$cart_item = new \ElggObject();
$cart_item->setSubtype('paypal_marketplace_cart_item');
$cart_item->owner_guid = elgg_get_logged_in_user_guid();
$cart_item->container_guid = $cart->guid;
$cart_item->access_id = ACCESS_PRIVATE;
$cart_item->title = $item->title;
$cart_item->item_guid = $item->guid;
$cart_item->price = $item->price;
$cart_item->currency = $item->currency;
$cart_item->transaction_type = $item->transaction_type;

if (!$cart_item->save()) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:add_item'));
}

// Success message
elgg_register_success_message(elgg_echo('paypal_marketplace:app:success:add'));

// Forward to cart page
return elgg_redirect_response('paypal_marketplace/cart'); 