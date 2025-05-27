<?php

namespace Elgg\PayPalMarketplace\Menus;

use Elgg\Menu\MenuItems;

class Site {
    
    /**
     * Register menu items for the site menu
     *
     * @param \Elgg\Event $event 'register', 'menu:site'
     *
     * @return MenuItems
     */
    public static function register(\Elgg\Event $event): MenuItems {
        $return = $event->getValue();
        
        $return[] = \ElggMenuItem::factory([
            'name' => 'paypal_marketplace',
            'text' => elgg_echo('paypal_marketplace:menu:title'),
            'href' => elgg_generate_url('collection:object:paypal_marketplace_item:all'),
            'icon' => 'shopping-cart',
        ]);
        
        return $return;
    }
} 