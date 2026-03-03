<?php
// action.php — Routes all instructor POST actions
// All forms POST here with a hidden 'action' field

require_once __DIR__ . '/config/app.php';
require_once 'controllers/InstructorController.php';

$ctrl   = new InstructorController();
$action = $_POST['action'] ?? '';

match($action) {
    'add_student'    => $ctrl->addStudent(),
    'update_student' => $ctrl->updateStudent(),
    'delete_student' => $ctrl->deleteStudent(),
    'add_course'     => $ctrl->addCourse(),
    'update_course'  => $ctrl->updateCourse(),
    'delete_course'  => $ctrl->deleteCourse(),
    'save_grade'     => $ctrl->saveGrade(),
    'delete_grade'   => $ctrl->deleteGrade(),
    default          => redirect('/views/instructor/dashboard.php'),
};