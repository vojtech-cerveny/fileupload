<?php
include 'vendor/autoload.php';

use Dilab\Network\SimpleRequest;
use Dilab\Network\SimpleResponse;
use Dilab\Resumable;

$request = new SimpleRequest();
$response = new SimpleResponse();

$resumable = new Resumable($request, $response);
$resumable->tempFolder = 'tmps';
$resumable->uploadFolder = 'uploads';
$resumable->process();