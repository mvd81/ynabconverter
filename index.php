<?php
//$file = 'C:\Users\Marcel\Desktop\6848448_06-10-2014_09-10-2014.csv';

$archive = TRUE;

include_once('bank.class.php');
include_once('ynab.class.php');
include_once('ing.class.php');
include_once('sns.class.php');


// ***** ING *******************************************************************************************************
$ing = new \ingClass\ing();
$mapping = array(0, '', 8, 5, 6);
$bank_name = 'ing';
$ing->set_mapping($mapping);

$filename = create_file_name('ynab', $bank_name);

// Auto create files from a directory
$dir = new DirectoryIterator($bank_name);
$ynab = new \ynabClass\ynab();
$ynab->scan_dir_and_create_files($dir, $ing, $filename, $archive);

// By hand
/*
$file = 'C:\Users\Marcel\Desktop\6848448_06-10-2014_09-10-2014.csv';
$ynab = new \ynabClass\ynab();
$ynab->set_file($file, $ing, $filename);
$ynab->create_ynab_file();
*/

// ***** SNS *******************************************************************************************************

$sns = new \snsClass\sns();
$mapping = array(0, '', 17, 10);
$bank_name = 'sns';
$sns->set_mapping($mapping);

$filename = create_file_name('ynab', $bank_name);

$dir = new DirectoryIterator($bank_name);
$ynab = new \ynabClass\ynab();
$ynab->scan_dir_and_create_files($dir, $sns, $filename, $archive);




// Create filename
function create_file_name($folder, $bank_name) {
  return $folder . '/' . $bank_name . '_' . date('Ymdhs') . '.csv';
}
