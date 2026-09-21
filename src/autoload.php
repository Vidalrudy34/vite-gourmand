<?php

spl_autoload_register(function ($class) {
    $dirs = ['config', 'helpers', 'models', 'controllers'];
    foreach ($dirs as $dir) {
        $file = __DIR__ . "/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Charge le package composer mongodb/mongodb s'il est installé
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
