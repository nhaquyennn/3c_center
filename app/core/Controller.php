<?php

class Controller {

    protected function view($module, $view, $data = []) {
        extract($data);
        require_once "../modules/$module/views/$view.php";
    }

}