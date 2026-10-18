<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$auth->logout();

header("Location: index.php");
exit();
