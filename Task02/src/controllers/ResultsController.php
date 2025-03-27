<?php

namespace neospaer\GCD\Controllers;

use neospaer\GCD\Model;

class ResultsController {
    private $model;

    public function __construct() {
        $this->model = new Model();
    }

    public function handleRequest() {
        $results = $this->model->getGameResults();
        require_once __DIR__ . '/../views/results.html.php';
    }
}
