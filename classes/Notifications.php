<?php

namespace Elgg\PayPalMarketplace;

class Notifications {
    
    /**
     * Send notification when a marketplace item is created
     *
     * @param \Elgg\Event $event 'create', 'object'
     *
     * @return void
     */
    public static function createItem(\Elgg\Event $event): void {
        $object = $event->getObject();
        if (!$object instanceof \ElggObject || $object->getSubtype() !== 'paypal_marketplace_item') {
            return;
        }
        
        // Notify site admins about new marketplace item
        $admins = elgg_get_admins();
        foreach ($admins as $admin) {
            notify_user(
                $admin->guid,
                $object->owner_guid,
                elgg_echo('paypal_marketplace:notification:create:subject'),
                elgg_echo('paypal_marketplace:notification:create:body', [
                    $object->title,
                    $object->getOwnerEntity()->getDisplayName(),
                    $object->getURL(),
                ])
            );
        }
    }
    
    /**
     * Send notification when a marketplace item is updated
     *
     * @param \Elgg\Event $event 'update', 'object'
     *
     * @return void
     */
    public static function updateItem(\Elgg\Event $event): void {
        $object = $event->getObject();
        if (!$object instanceof \ElggObject || $object->getSubtype() !== 'paypal_marketplace_item') {
            return;
        }
        
        // Notify site admins about updated marketplace item
        $admins = elgg_get_admins();
        foreach ($admins as $admin) {
            notify_user(
                $admin->guid,
                $object->owner_guid,
                elgg_echo('paypal_marketplace:notification:update:subject'),
                elgg_echo('paypal_marketplace:notification:update:body', [
                    $object->title,
                    $object->getOwnerEntity()->getDisplayName(),
                    $object->getURL(),
                ])
            );
        }
    }
    
    /**
     * Send notification when a marketplace item is deleted
     *
     * @param \Elgg\Event $event 'delete', 'object'
     *
     * @return void
     */
    public static function deleteItem(\Elgg\Event $event): void {
        $object = $event->getObject();
        if (!$object instanceof \ElggObject || $object->getSubtype() !== 'paypal_marketplace_item') {
            return;
        }
        
        // Notify site admins about deleted marketplace item
        $admins = elgg_get_admins();
        foreach ($admins as $admin) {
            notify_user(
                $admin->guid,
                $object->owner_guid,
                elgg_echo('paypal_marketplace:notification:delete:subject'),
                elgg_echo('paypal_marketplace:notification:delete:body', [
                    $object->title,
                    $object->getOwnerEntity()->getDisplayName(),
                ])
            );
        }
    }
} 