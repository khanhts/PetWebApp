<?php
require_once './models/Customer.php';
require_once 'config/database.php';

class CustomerController {
    private $customerModel;

    public function __construct() {
        global $pdo;
        $this->customerModel = new Customer($pdo);
    }

    public function listCustomerIds() {
        $customerIds = $this->customerModel->getAllCustomerIds();
        echo "<pre>";
        print_r($customerIds);
        echo "</pre>";
    }
}
?>
