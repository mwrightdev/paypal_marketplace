<?php

namespace Elgg\PayPalMarketplace\Menus;

use Elgg\Menu\MenuItems;

class Title {
    
    /**
     * Register menu items for the title menu
     *
     * @param \Elgg\Event $event 'register', 'menu:title'
     *
     * @return MenuItems
     */
    public static function register(\Elgg\Event $event): MenuItems {
        $return = $event->getValue();
        
        $page_owner = elgg_get_page_owner_entity();
        $user = elgg_get_logged_in_user_entity();
        
        if (!$user) {
            return $return;
        }
        
        // Add "Add Item" button on marketplace listing page
        if (elgg_in_context('paypal_marketplace') && !elgg_in_context('add') && !elgg_in_context('edit')) {
            $return[] = \ElggMenuItem::factory([
                'name' => 'add',
                'text' => elgg_echo('paypal_marketplace:add'),
                'href' => elgg_generate_url('add:object:paypal_marketplace_item'),
                'link_class' => 'elgg-button elgg-button-action',
            ]);
        }

        if (elgg_in_context('paypal_marketplace') && !elgg_in_context('cart')) {
            $return[] = \ElggMenuItem::factory([
                'name' => 'cart',
                'text' => elgg_echo('paypal_marketplace:app'),
                'href' => elgg_generate_url('cart:object:paypal_marketplace_item'),
                'link_class' => 'elgg-button elgg-button-action',
            ]);
        }
        
        return $return;
    }
} 