<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\GatekeeperException;

$guid = get_input('guid');
$title = get_input('title');
$description = get_input('description');
$transaction_type = get_input('transaction_type');
$price = get_input('price');
$currency = get_input('currency');
$images = get_input('images', []);

// Validate required fields
if (empty($title) || empty($description) || empty($transaction_type) || empty($price) || empty($currency)) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:required'));
}

// Validate price
if (!is_numeric($price) || $price < 0) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:invalid_price'));
}

// Validate currency
$valid_currencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY'];
if (!in_array($currency, $valid_currencies)) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:invalid_currency'));
}

// Validate transaction type
$valid_types = ['buy', 'sell', 'rent', 'trade', 'auction', 'gift', 'donate'];
if (!in_array($transaction_type, $valid_types)) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:invalid_type'));
}

// Get or create entity
if ($guid) {
    $entity = get_entity($guid);
    if (!$entity instanceof \ElggObject || $entity->getSubtype() !== 'paypal_marketplace_item') {
        throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:invalid_entity'));
    }
    
    // Check if user can edit
    if (!$entity->canEdit()) {
        throw new GatekeeperException(elgg_echo('paypal_marketplace:save:error:permission'));
    }
} else {
    $entity = new \ElggObject();
    $entity->setSubtype('paypal_marketplace_item');
    $entity->owner_guid = elgg_get_logged_in_user_guid();
    $entity->container_guid = elgg_get_logged_in_user_guid();
    $entity->access_id = ACCESS_PUBLIC;
}

// Set basic properties
$entity->title = $title;
$entity->description = $description;
$entity->transaction_type = $transaction_type;
$entity->price = $price;
$entity->currency = $currency;

// Set transaction type specific properties
switch ($transaction_type) {
    case 'auction':
        $end_date = get_input('auction_end_date');
        $min_bid = get_input('auction_min_bid');
        
        if (empty($end_date) || $end_date < time()) {
            throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:invalid_auction_end_date'));
        }
        
        if (!empty($min_bid) && (!is_numeric($min_bid) || $min_bid < 0)) {
            throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:invalid_min_bid'));
        }
        
        $entity->auction_end_date = $end_date;
        $entity->auction_min_bid = $min_bid;
        break;
        
    case 'rent':
        $rent_period = get_input('rent_period');
        if (!in_array($rent_period, ['hour', 'day', 'week', 'month'])) {
            throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:invalid_rent_period'));
        }
        $entity->rent_period = $rent_period;
        break;
        
    case 'trade':
        $trade_description = get_input('trade_description');
        if (empty($trade_description)) {
            throw new BadRequestException(elgg_echo('paypal_marketplace:save:error:required_trade_description'));
        }
        $entity->trade_description = $trade_description;
        break;
        
    case 'gift':
    case 'donate':
        $entity->anonymous = get_input('anonymous') === 'yes' ? 'yes' : 'no';
        break;
}

// Save entity
if (!$entity->save()) {
    throw new BadRequestException(elgg_echo('paypal_marketplace:save:error'));
}

// Handle image uploads
if (!empty($images)) {
    foreach ($images as $image) {
        if ($image['error'] === UPLOAD_ERR_OK) {
            $file = new \ElggFile();
            $file->owner_guid = $entity->guid;
            $file->setFilename("marketplace/{$entity->guid}/" . time() . ".jpg");
            
            // Get image data
            $image_data = file_get_contents($image['tmp_name']);
            if (!$image_data) {
                continue;
            }
            
            // Save image
            $file->open('write');
            $file->write($image_data);
            $file->close();
            
            // Create image entity
            $image_entity = new \ElggObject();
            $image_entity->setSubtype('paypal_marketplace_image');
            $image_entity->owner_guid = $entity->guid;
            $image_entity->container_guid = $entity->guid;
            $image_entity->access_id = ACCESS_PUBLIC;
            $image_entity->title = $entity->title;
            $image_entity->description = $entity->description;
            $image_entity->filename = $file->getFilename();
            $image_entity->mime_type = $image['type'];
            $image_entity->simpletype = 'image';
            
            if (!$image_entity->save()) {
                $file->delete();
                continue;
            }
            
            // Add relationship
            $entity->addRelationship($image_entity->guid, 'image');
        }
    }
}

// Success message
elgg_register_success_message(elgg_echo('paypal_marketplace:save:success'));

// Forward to view page
return elgg_redirect_response($entity->getURL()); 