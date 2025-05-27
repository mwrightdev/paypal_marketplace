<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\PayPalMarketplace\PayPalClient;

// Get transaction ID from session
$transaction_id = elgg_get_session()->get('paypal_transaction_id');
if (empty($transaction_id)) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:callback:error:no_transaction'));
}

// Get transaction
$transaction = get_entity($transaction_id);
if (!$transaction instanceof \ElggObject || $transaction->getSubtype() !== 'paypal_marketplace_transaction') {
    throw new BadRequestException(elgg_echo('paypal_marketplace:callback:error:invalid_transaction'));
}

// Get item
$item = get_entity($transaction->item_guid);
if (!$item instanceof \ElggObject || $item->getSubtype() !== 'paypal_marketplace_item') {
    throw new BadRequestException(elgg_echo('paypal_marketplace:callback:error:invalid_item'));
}

// Get PayPal order ID
$order_id = get_input('token');
if (empty($order_id) || $order_id !== $transaction->paypal_order_id) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:callback:error:invalid_order'));
}

// Initialize PayPal client
$paypal = new PayPalClient();

try {
    // Capture the payment
    $capture = $paypal->captureOrder($order_id);
    
    if (empty($capture['id']) || $capture['status'] !== 'COMPLETED') {
        throw new BadRequestException(elgg_echo('paypal_marketplace:callback:error:capture_failed'));
    }
    
    // Update transaction status
    $transaction->status = 'completed';
    $transaction->paypal_capture_id = $capture['id'];
    $transaction->completed_time = time();
    
    if (!$transaction->save()) {
        throw new BadRequestException(elgg_echo('paypal_marketplace:callback:error:update_transaction'));
    }
    
    // Process seller payout
    $seller = get_entity($item->owner_guid);
    if ($seller && $seller->paypal_email) {
        // Calculate platform fee
        $fee_percentage = elgg_get_plugin_setting('fee_percentage', 'paypal_marketplace');
        $fee_amount = ($transaction->amount * $fee_percentage) / 100;
        $payout_amount = $transaction->amount - $fee_amount;
        
        // Create payout
        $payout = $paypal->createPayout(
            $seller->paypal_email,
            $payout_amount,
            $transaction->currency,
            "Payment for {$item->title}"
        );
        
        if (!empty($payout['batch_header']['payout_batch_id'])) {
            $transaction->payout_batch_id = $payout['batch_header']['payout_batch_id'];
            $transaction->payout_amount = $payout_amount;
            $transaction->fee_amount = $fee_amount;
            $transaction->save();
        }
    }
    
    // Notify seller
    $seller = get_entity($item->owner_guid);
    if ($seller) {
        $buyer = get_entity($transaction->owner_guid);
        $subject = elgg_echo('paypal_marketplace:notification:seller:subject');
        $message = elgg_echo('paypal_marketplace:notification:seller:body', [
            $item->title,
            $buyer->getDisplayName(),
            $transaction->amount,
            $transaction->currency,
        ]);
        
        notify_user($seller->guid, $buyer->guid, $subject, $message);
    }
    
    // Notify buyer
    $buyer = get_entity($transaction->owner_guid);
    if ($buyer) {
        $subject = elgg_echo('paypal_marketplace:notification:buyer:subject');
        $message = elgg_echo('paypal_marketplace:notification:buyer:body', [
            $item->title,
            $seller->getDisplayName(),
            $transaction->amount,
            $transaction->currency,
        ]);
        
        notify_user($buyer->guid, $seller->guid, $subject, $message);
    }
    
    // Clear session
    elgg_get_session()->remove('paypal_transaction_id');
    
    // Success message
    elgg_register_success_message(elgg_echo('paypal_marketplace:purchase:success'));
    
    // Forward to item page
    return elgg_redirect_response($item->getURL());
    
} catch (\Exception $e) {
    // Update transaction status
    $transaction->status = 'failed';
    $transaction->error_message = $e->getMessage();
    $transaction->save();
    
    throw new BadRequestException(elgg_echo('paypal_marketplace:callback:error', [$e->getMessage()]));
} 