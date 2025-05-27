<?php

namespace Elgg\PayPalMarketplace;

// use Elgg\DefaultPluginBootstrap;

// class Bootstrap extends DefaultPluginBootstrap {
    
//     public function init() {
//         // Register page handler
//         elgg_register_page_handler('paypal_marketplace', [$this, 'page_handler']);
        
//         // Register menu items
//         elgg_register_menu_item('site', [
//             'name' => 'paypal_marketplace',
//             'text' => elgg_echo('paypal_marketplace:menu:title'),
//             'href' => 'paypal_marketplace/all',
//             'icon' => 'shopping-cart',
//         ]);
        
//         // Register entity types
//         elgg_register_entity_type('object', 'paypal_marketplace_item');
        
//         // Register actions
//         elgg_register_action('paypal_marketplace/save', __DIR__ . '/actions/save.php');
//         elgg_register_action('paypal_marketplace/delete', __DIR__ . '/actions/delete.php');
//         elgg_register_action('paypal_marketplace/purchase', __DIR__ . '/actions/purchase.php');
//     }
    
//     public function page_handler($segments) {
//         $page = array_shift($segments);
        
//         switch ($page) {
//             case 'all':
//                 echo elgg_view_resource('paypal_marketplace/all');
//                 break;
                
//             case 'add':
//                 echo elgg_view_resource('paypal_marketplace/add');
//                 break;
                
//             case 'view':
//                 $guid = array_shift($segments);
//                 echo elgg_view_resource('paypal_marketplace/view', [
//                     'guid' => $guid,
//                 ]);
//                 break;
                
//             case 'edit':
//                 $guid = array_shift($segments);
//                 echo elgg_view_resource('paypal_marketplace/edit', [
//                     'guid' => $guid,
//                 ]);
//                 break;
                
//             default:
//                 return false;
//         }
        
//         return true;
//     }
// }

return [
    'plugin' => [
        'name' => 'PayPal Marketplace',
        'activate_on_install' => true,
    ],
    'actions' => [
        'paypal_marketplace/save' => [],
        'paypal_marketplace/delete' => [],
        'paypal_marketplace/purchase' => [],
        'paypal_marketplace/paypal_callback' => [],
        'paypal_marketplace/add' => [
            'access' => 'logged_in',
        ],
        'paypal_marketplace/cart/add' => [
            'access' => 'logged_in',
        ],
        'paypal_marketplace/cart/remove' => [
            'access' => 'logged_in',
        ],
    ],
    'routes' => [
        'collection:object:paypal_marketplace_item:all' => [
            'path' => '/paypal_marketplace/all',
            'resource' => 'paypal_marketplace/all',
        ],
        'add:object:paypal_marketplace_item' => [
            'path' => '/paypal_marketplace/add',
            'resource' => 'paypal_marketplace/add',
            'middleware' => [
                \Elgg\Router\Middleware\Gatekeeper::class,
            ],
        ],
        'view:object:paypal_marketplace_item' => [
            'path' => '/paypal_marketplace/view/{guid}',
            'resource' => 'paypal_marketplace/view',
        ],
        'edit:object:paypal_marketplace_item' => [
            'path' => '/paypal_marketplace/edit/{guid}',
            'resource' => 'paypal_marketplace/edit',
            'middleware' => [
                \Elgg\Router\Middleware\Gatekeeper::class,
            ],
        ],
        'cart:object:paypal_marketplace_item' => [
            'path' => '/paypal_marketplace/cart',
            'resource' => 'paypal_marketplace/app',
            'middleware' => [
                \Elgg\Router\Middleware\Gatekeeper::class,
            ],
        ],
    ],
    'events' => [
        'register' => [
            'menu:site' => [
                'Elgg\PayPalMarketplace\Menus\Site::register' => [],
            ],
            'menu:title' => [
                'Elgg\PayPalMarketplace\Menus\Title::register' => [],
            ],
            'menu:entity' => [
                'Elgg\PayPalMarketplace\Menus\Entity::register' => [],
            ],
        ],
        'create' => [
            'object' => [
                'Elgg\PayPalMarketplace\Notifications::createItem' => [],
            ],
        ],
        'update' => [
            'object' => [
                'Elgg\PayPalMarketplace\Notifications::updateItem' => [],
            ],
        ],
        'delete' => [
            'object' => [
                'Elgg\PayPalMarketplace\Notifications::deleteItem' => [],
            ],
        ],
    ],
    'view_extensions' => [
        'elgg.css' => [
            'paypal_marketplace/css' => [],
        ],
    ],
    'view_options' => [
        'forms/paypal_marketplace/edit' => ['ajax' => true],
    ],
    'settings' => [
        'paypal_client_id' => '',
        'paypal_client_secret' => '',
        'paypal_sandbox' => 'yes',
        'currency' => 'USD',
        'fee_percentage' => 0,
    ],
]; 