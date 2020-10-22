<?php

use wcf\system\package\PackageInstallationDispatcher;
use wcf\system\WCF;

/** @var PackageInstallationDispatcher $installation */
$installation = $this->installation;
$package = $installation->getPackage();

$sql = "SELECT *
        FROM   wcf".WCF_N."_package_installation_file_log
        WHERE  packageID = ?
          AND  filename IN (?, ?)";
$selectStatement = WCF::getDB()->prepareStatement($sql);
$selectStatement->execute([
	$package->packageID,
	'files_wcf/js/Bastelstu.be/_Push.js',
	'files_wcf/lib/system/push/PushHandler.class.php',
]);

$sql = "DELETE FROM wcf".WCF_N."_package_installation_file_log
        WHERE       packageID = ? AND filename = ?";
$deleteStatement = WCF::getDB()->prepareStatement($sql);

while (($row = $selectStatement->fetchArray())) {
	$fileName = WCF_DIR.$row['filename'];
	if (file_exists($fileName)) {
		unlink($fileName);
	}
	
	$deleteStatement->execute([
		$package->packageID,
		$row['filename'],
	]);
}

foreach ([
	WCF_DIR.'files_wcf/js/Bastelstu.be/',
	WCF_DIR.'files_wcf/js/',
	WCF_DIR.'files_wcf/lib/system/push/',
	WCF_DIR.'files_wcf/lib/system/',
	WCF_DIR.'files_wcf/lib/',
	WCF_DIR.'files_wcf/',
] as $directory) {
	@rmdir($directory);
}
