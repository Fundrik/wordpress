<?php

declare(strict_types=1);

define( 'FUNDRIK_MAIN_FILE', realpath( __DIR__ . '/../fundrik.php' ) );

define( 'FUNDRIK_PATH', realpath( dirname( FUNDRIK_MAIN_FILE ) ) . '/' );

require_once __DIR__ . '/../vendor/autoload.php';
