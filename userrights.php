<?php

// define base path
// php can't read 'äöå' so we need this symlink
static $BASE_PATH = "/root/kehityskeskustelut/";

// change to current working directory
// we do this again for all the individual yksiköt later on
chdir($BASE_PATH);

/*
 * All files need to deny read/write for others
 * All files need to allow read for user and group
 * Some files require write permissions to user and group,
 * Some files require write permissions only to group
 * 	user and group = työntekijä ja esimies, 0770
 *  group = esimies, 0570
 */
// Vuodelle 2014
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/01\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/02\-*");
exec("chmod 0570 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/03\-*");
exec("chmod 0570 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/04\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/05\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/06\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/07\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2014/08\-*");
// Vuodelle 2015
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/01\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/02\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/03\-*");
exec("chmod 0570 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/04\-*");
exec("chmod 0570 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/05\-*");
exec("chmod 0570 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/06\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/07\-*");
exec("chmod 0770 ./*/*/Tulos-\ ja\ kehityssuunnitelma\ 2015/08\-*");


// lists all the subdirectories, such as 10-Hallinto
// php is already in the base path
$subdirectories = glob("*",GLOB_ONLYDIR);
foreach ($subdirectories as $subdirectory)
{
	// ignore this directory, as it's irrelevant
	if ($subdirectory == "00-ADMIN") continue;

	// enter the subdirectory
	chdir($BASE_PATH . $subdirectory);

	// removes incorrect o+rwx rights from the employee base dirs
	exec("chmod 2770 *");

	// list all the directories, the employees
	$employees = glob("*",GLOB_ONLYDIR);

	/*
	 * For each employee set the correct chown to all files and directories.
	 * This script doesn't know or care who the owner should be,
	 * this only enforces the user:group to the contents of the directory.
	 * When esimes edits files, the owner changes and the employee can
	 * no longer read the file, this fixes it
	 */
	foreach ($employees as $employee)
	{
		// reads the directory owner and resolves username
		$pwuid = posix_getpwuid(fileowner($employee));
		// reads the directory's group and resolves group name
		$grgid = posix_getgrgid(filegroup($employee));

		// if name is not resolved, root is used
		if($pwuid['name'] === FALSE) $pwuid['name'] = "root";
		if($grgid['name'] === FALSE) $grgid['name'] = "root";

		// looks like this: chown kalle:esimiesHLP Kallio\ Kalle -R
		exec("chown " . $pwuid['name'] . ":" . $grgid['name'] . " " . str_replace(" ", "\ ",$employee) . " -R");
	}

}

?>
