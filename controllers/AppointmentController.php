<?php
require_once './models/Appointment.php';
require_once './models/User.php';
require_once 'config/database.php';

class AppointmentController {
    private $appointmentModel;
    private $userModel;


    public function __construct() {
        global $pdo;
        $this->appointmentModel = new Appointment($pdo);
        $this->userModel = new User($pdo);
    }

    public function index() {
        $appointments = $this->appointmentModel->getAppointmentsByCustomer(1);
        require './views/appointments/index.php';
    }

    public function detail($id) {
        $appointment = $this->appointmentModel->getAppointmentById($id);
        $vet = $this->userModel->getVetById($appointment['vet_id']);
        require './views/appointments/detail.php';
    }

    public function cancel($id) {
        $this->appointmentModel->cancelAppointment($id);
        header("Location: index.php?controller=appointments&action=index");
        exit;

    }
}
?>
