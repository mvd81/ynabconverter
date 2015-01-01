<?php
namespace snsClass;

use bankClass\bank;
//use ziparchive;

class sns implements bank {

  private $mapping;

  /**
   * Set mapping to filter the data from a file row
   * Example if we only want array key 1, 6 and 2 $mapping = array(1,6,2);
   * @param bool $mapping
   * @return bool
   */
  public function set_mapping($mapping = FALSE) {
    $this->mapping = $mapping;
  }

  public function get_mapping() {
    if (is_array($this->mapping)) {
      return $this->mapping;
    }
    return FALSE;
  }

  public function get_bank_data($data) {
    $new_data = array();
    $mapping = $this->get_mapping();
    foreach ($data as $record_id => $file_row) {

      foreach ($mapping as $mapping_key) {

        // 0 doesn't work in switches
        if ($mapping_key === 0) {
          // Date
          $time_stamp = strtotime($file_row[$mapping_key]);
          $new_data[$record_id][] = date('d/m/Y', $time_stamp);
        }

        switch ($mapping_key) {
          case '':
            // Empty line
            $new_data[$record_id][] = '';
            break;
          case 10:
            // Price
            $float = floatval($file_row[$mapping_key]);

            // Check if float is positive/negative
            if ($float > 0) {
              $new_data[$record_id][] = '';
              $new_data[$record_id][] = $file_row[$mapping_key];
            }
            else {
              $new_data[$record_id][] = str_replace('-', '', $file_row[$mapping_key]);
              $new_data[$record_id][] = '';
            }

            $new_data[$record_id][] = '';
            break;
          case 8:
            $new_data[$record_id][] = str_replace('  ', '', trim($file_row[$mapping_key]));
            break;
          case 17:
            $new_data[$record_id][] = str_replace('  ', '', trim($file_row[$mapping_key]));
            break;
        }
      }
    }
    return $new_data;
  }

  /**
   * Scan directory for sns zip export files
   * @param $dir
   * @return array
   */
  public function get_files_from_dir($dir) {
    $zip = new \ziparchive();
    $files = array();
    foreach ($dir as $fileinfo) {
      if (!$fileinfo->isDot()) {

        if ($fileinfo->getExtension() == 'zip') {

          // Try to open the zip file
          if ($zip->open($fileinfo->getPathname()) === TRUE) {

            $zip->extractTo('sns');
            $zip->close();

            // Remove this zip file
            unlink($fileinfo->getPathname());

            // Check now for a csv file
            foreach($dir as $scv_scan) {
              if (!$scv_scan->isDot()) {
                if ($scv_scan->getExtension() == 'CSV') {
                  $files[] = $scv_scan->getPathname();
                }
              }
            }
          }
        }
      }
    }
    return $files;
  }
}