$router->get('/appointments', 'AppointmentController@index');
$router->get('/appointments/detail/{id}', 'AppointmentController@detail');
$router->get('/appointments/cancel/{id}', 'AppointmentController@cancel');
$router->get('/customers/ids', 'CustomerController@listCustomerIds');
