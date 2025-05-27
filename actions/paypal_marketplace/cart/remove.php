<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\GatekeeperException;

// Ensure user is logged in
elgg_gatekeeper();

$guid = get_input('guid');
$cart_item = get_entity($guid);

if (!$cart_item instanceof \ElggObject || $cart_item->getSubtype() !== 'paypal_marketplace_cart_item') {
    throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:invalid_item'));
}

// Check if user owns the cart
$cart = get_entity($cart_item->container_guid);
if (!$cart || $cart->owner_guid !== elgg_get_logged_in_user_guid()) {
    throw new GatekeeperException(elgg_echo('paypal_marketplace:app:error:permission'));
}

// Delete cart item
if (!$cart_item->delete()) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:app:error:remove'));
}

// Success message
elgg_register_success_message(elgg_echo('paypal_marketplace:app:success:remove'));

// Forward back to cart
return elgg_redirect_response('paypal_marketplace/cart'); 