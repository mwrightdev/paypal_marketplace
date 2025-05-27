<?php

namespace Elgg\PayPalMarketplace\Menus;

use Elgg\Menu\MenuItems;

class Entity {
    
    /**
     * Register menu items for entity menu
     *
     * @param \Elgg\Event $event 'register', 'menu:entity'
     *
     * @return MenuItems
     */
    public static function register(\Elgg\Event $event): MenuItems {
        $return = $event->getValue();
        
        $entity = $event->getEntityParam();
        if (!$entity instanceof \ElggObject || $entity->getSubtype() !== 'paypal_marketplace_item') {
            return $return;
        }
        
        $user = elgg_get_logged_in_user_entity();
        if (!$user) {
            return $return;
        }

        $return[] = \ElggMenuItem::factory([
            'name' => 'add_to_cart',
            'text' => elgg_echo('Add to Cart'),
            'href' => elgg_generate_action_url('paypal_marketplace/cart/add', [
                'guid' => $entity->guid,
            ]),
            'icon' => 'cart-plus',
        ]);
        
        // Owner actions
        if ($entity->canEdit()) {
            $return[] = \ElggMenuItem::factory([
                'name' => 'edit',
                'text' => elgg_echo('edit'),
                'href' => elgg_generate_url('edit:object:paypal_marketplace_item', [
                    'guid' => $entity->guid,
                ]),
                'icon' => 'edit',
            ]);
            
            $return[] = \ElggMenuItem::factory([
                'name' => 'delete',
                'text' => elgg_echo('delete'),
                'href' => elgg_generate_action_url('paypal_marketplace/delete', [
                    'guid' => $entity->guid,
                ]),
                'icon' => 'delete',
                'confirm' => elgg_echo('deleteconfirm'),
            ]);
        }
        
        // Buyer actions
        if ($user->guid !== $entity->owner_guid) {
            switch ($entity->transaction_type) {
                case 'buy':
                case 'sell':
                case 'rent':
                    $return[] = \ElggMenuItem::factory([
                        'name' => 'purchase',
                        'text' => elgg_echo('paypal_marketplace:action:purchase'),
                        'href' => elgg_generate_action_url('paypal_marketplace/purchase', [
                            'guid' => $entity->guid,
                        ]),
                        'icon' => 'shopping-cart',
                    ]);
                    break;
                    
                case 'auction':
                    $return[] = \ElggMenuItem::factory([
                        'name' => 'bid',
                        'text' => elgg_echo('paypal_marketplace:action:bid'),
                        'href' => elgg_generate_url('paypal_marketplace/bid', [
                            'guid' => $entity->guid,
                        ]),
                        'icon' => 'gavel',
                    ]);
                    break;
                    
                case 'gift':
                case 'donate':
                    $return[] = \ElggMenuItem::factory([
                        'name' => 'donate',
                        'text' => elgg_echo('paypal_marketplace:action:donate'),
                        'href' => elgg_generate_url('paypal_marketplace/donate', [
                            'guid' => $entity->guid,
                        ]),
                        'icon' => 'gift',
                    ]);
                    break;
                    
                case 'trade':
                    $return[] = \ElggMenuItem::factory([
                        'name' => 'trade',
                        'text' => elgg_echo('paypal_marketplace:action:trade'),
                        'href' => elgg_generate_url('paypal_marketplace/trade', [
                            'guid' => $entity->guid,
                        ]),
                        'icon' => 'exchange',
                    ]);
                    break;
            }
        }
        
        return $return;
    }
} 