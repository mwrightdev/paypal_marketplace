<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\GatekeeperException;
use Elgg\PayPalMarketplace\PayPalClient;

$guid = get_input('guid');
$item = get_entity($guid);

if (!$item instanceof \ElggObject || $item->getSubtype() !== 'paypal_marketplace_item') {
    throw new BadRequestException(elgg_echo('paypal_marketplace:purchase:error:invalid_item'));
}

// Check if user is logged in
if (!elgg_is_logged_in()) {
    throw new GatekeeperException(elgg_echo('paypal_marketplace:purchase:error:login_required'));
}

// Check if user is not the owner
if ($item->owner_guid === elgg_get_logged_in_user_guid()) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:purchase:error:own_item'));
}

// Check if item is available for purchase
if (!in_array($item->transaction_type, ['buy', 'sell', 'rent'])) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:purchase:error:invalid_type'));
}

// Initialize PayPal client
$paypal = new PayPalClient();

try {
    // Create PayPal order
    $order = $paypal->createOrder(
        $item->price,
        $item->currency,
        $item->title
    );
    
    if (empty($order['id'])) {
        throw new BadRequestException(elgg_echo('paypal_marketplace:purchase:error:paypal_order'));
    }
    
    // Create transaction record
    $transaction = new \ElggObject();
    $transaction->setSubtype('paypal_marketplace_transaction');
    $transaction->owner_guid = elgg_get_logged_in_user_guid();
    $transaction->container_guid = $item->guid;
    $transaction->access_id = ACCESS_PRIVATE;
    $transaction->title = "Transaction for {$item->title}";
    $transaction->description = "PayPal Order ID: {$order['id']}";
    $transaction->paypal_order_id = $order['id'];
    $transaction->item_guid = $item->guid;
    $transaction->amount = $item->price;
    $transaction->currency = $item->currency;
    $transaction->status = 'pending';
    
    if (!$transaction->save()) {
        throw new BadRequestException(elgg_echo('paypal_marketplace:purchase:error:save_transaction'));
    }
    
    // Add relationship between item and transaction
    $item->addRelationship($transaction->guid, 'transaction');
    
    // Store transaction ID in session for later use
    elgg_get_session()->set('paypal_transaction_id', $transaction->guid);
    
    // Return PayPal order approval URL
    $approval_url = '';
    foreach ($order['links'] as $link) {
        if ($link['rel'] === 'approve') {
            $approval_url = $link['href'];
            break;
        }
    }
    
    if (empty($approval_url)) {
        throw new BadRequestException(elgg_echo('paypal_marketplace:purchase:error:approval_url'));
    }
    
    // Forward to PayPal
    return elgg_redirect_response($approval_url);
    
} catch (\Exception $e) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:purchase:error', [$e->getMessage()]));
} 