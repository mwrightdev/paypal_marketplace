<?php

elgg_gatekeeper();

$title = elgg_echo('paypal_marketplace:add:title');

$content = elgg_view_form('paypal_marketplace/add', [
    'enctype' => 'multipart/form-data',
    'action' => 'action/paypal_marketplace/save',
]);

$layout = elgg_view_layout('default', [
    'title' => $title,
    'content' => $content,
    'filter' => false,
]);

echo elgg_view_page($title, $layout); 