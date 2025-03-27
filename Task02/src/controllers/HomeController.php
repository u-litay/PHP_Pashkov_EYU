<?php

namespace neospaer\GCD\Controllers;

class HomeController {
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_name'])) {

            $_SESSION['player_name'] = $_POST['player_name'];
            header('Location: index.php?page=game');
            exit();
        }
        require_once __DIR__ . '/../views/home.html.php';
    }
}
