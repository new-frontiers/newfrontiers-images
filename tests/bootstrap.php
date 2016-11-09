<?php
/**
 * Copyright (c) new frontiers Software GmbH
 */

// Setzen des Working-Dir für die PHPUnit Tests
// und registrieren des Autoloaders

chdir(__DIR__ . '/../src');
require __DIR__ . '/../vendor/autoload.php';

// PHP Einstellungen anpassen
setlocale(LC_ALL, 'german', 'de_DE', 'de', 'deu');
date_default_timezone_set('Europe/Berlin');
mb_internal_encoding('UTF-8');
