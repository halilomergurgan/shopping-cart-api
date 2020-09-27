<?php
// Routes

$app->get('/products', 'Get');

$app->get('/users[/{userId}/cart]', 'Cart');

$app->post('/users[/{userId}/cart]', 'Store');

$app->post('/users/{userId}/{purchase}', 'Purchase');

$app->get('/user[/{userId}/orders]', 'Order');
