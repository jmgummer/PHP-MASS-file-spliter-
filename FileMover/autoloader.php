<?php
date_default_timezone_set('Africa/Nairobi');
include 'public/settings/settings.php';
// Using an anonymous function
spl_autoload_register(function($class){
    include_once('public/lib/' . $class . '.php');
});

$runner = new FileMover;