<?php
/*******************************************************************
 *
 * Name = YNAB CLASS
 * Author = Marcel van Doornen
 * Date = 10-10-2014
 *
 * Class to convert bank csv(s) to a format for YNBAN @link http://www.youneedabudget.com/
 *
 *******************************************************************/

namespace ynabClass;

class ynab {

  private $data;
  private $file;
  private $bank_class;
  private $new_file_name;
  public $errors = array();

  /**
   * Set the file with the bank export data
   * @param $file
   * @param $bank_class
   * @param $new_file
   */
  public function set_file($file, $bank_class, $new_file) {
    $this->file = $file;
    $this->bank_class = $bank_class;
    $this->new_file_name = $new_file;
  }

  /**
   * Scan directory to create ynab files
   *
   * @param $dir
   * @param $bank_class
   * @param $filename
   * @param $archive
   */
  public function scan_dir_and_create_files($dir, $bank_class, $filename, $archive) {
    $result = array();
    // Scan directory
    foreach ($bank_class->get_files_from_dir($dir) as $file) {
      //var_dump($file);
      $this->auto_create_file($file, $bank_class, $filename, $archive);
    }
  }

  /**
   * Automatic create ynab file and archive or delete the original bank file
   *
   * @param $file
   * @param $bank_class
   * @param $filename
   * @param $archive
   */
  private function auto_create_file($file, $bank_class, $filename, $archive) {
    $this->set_file($file, $bank_class, $filename, $archive);
    if ($this->create_ynab_file()) {
      // Move file to archive folder
      if ($archive) {
        rename($file, 'archive\\' . $file);
      }
      else {
        // Delete file
        unlink($file);
      }
    }
    //Reset data
    $this->reset_data();
  }

  /**
   * Get the file data
   */
  function get_file_data() {
    if (file_exists($this->file)) {
      $this->data = array_map('str_getcsv', file($this->file));
      return $this->data;
    }
    $this->data = FALSE;
    // Set error file doesn't exists
    $this->errors[] = $this->file . " doesn't exists";
    return FALSE;
  }

  /**
   * Create file for YNAB
   */
  public function create_ynab_file() {
    // Get data if we don't have the file data yet
    if (empty($this->data)) {
      $this->data = $this->get_file_data();
    }

    // Check for data and if mapping is set
    if ($this->data && $this->bank_class->get_mapping()) {

      // Get the processed data
      $new_data = $this->bank_class->get_bank_data($this->data);

      // Check if we have some data to create the file for into ynab
      if (!empty($new_data)) {
        if ($this->write_ynab_file($new_data)) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Write file to import into YNAB
   * @param $data
   * @return bool
   */
  private function write_ynab_file($data) {
    var_dump($data);
    $file = fopen($this->new_file_name, "w");

    // Write headers
    fwrite($file, 'Date,Payee,Category,Memo,Outflow,Inflow' ."\n");

    foreach ($data as $record) {
      fwrite($file, implode($record, ',') . "\n");
    }
    fclose($file);

    if (file_exists($this->new_file_name)) {
      return TRUE;
    }
    return FALSE;
  }

  public function get_errors() {
    return $this->errors;
  }

  private function reset_data() {
    $this->file = NULL;
    $this->data = NULL;
    $this->bank_class = NULL;
  }
}