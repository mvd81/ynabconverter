<?php
print '<h1>YNAB converter</h1>';

$archive = TRUE;

include_once('bank.class.php');
include_once('ynab.class.php');
include_once('ing.class.php');
include_once('sns.class.php');


// ***** ING *******************************************************************************************************
$ing = new \ingClass\ing();
$mapping = array(0, 1, 99, 8, 5, 6);
$bank_name = 'ing';
$ing->set_mapping($mapping);

$filename = create_file_name('ynab', $bank_name);

// Auto create files from a directory
$dir = new DirectoryIterator($bank_name);
$ynab = new \ynabClass\ynab();
$ynab->scan_dir_and_create_files($dir, $ing, $filename, $archive);

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
