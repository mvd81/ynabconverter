<?php
namespace ingClass;

use bankClass\bank;

class ing implements bank {

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

      // Skip header
      if ($record_id > 0) {

        foreach ($mapping as $mapping_key) {

          $price = $file_row[end($mapping)];

          // 0 doesn't work in switches
          if ($mapping_key === 0) {
            // Date
            $time_stamp = strtotime($file_row[$mapping_key]);
            $new_data[$record_id][] = date('d/m/Y', $time_stamp);
          }

          switch ($mapping_key) {
            case 99:
              // Empty line
              $new_data[$record_id][] = '';
              break;
            case 5:
              // Price
              if ($file_row[$mapping_key] == 'Af') {
                $new_data[$record_id][] = str_replace(',', '.', $price);
                $new_data[$record_id][] = '';
              } else {
                $new_data[$record_id][] = '';
                $new_data[$record_id][] = str_replace(',', '.', $price);
              }
              $new_data[$record_id][] = '';
              break;
            case 1:
            case 8:
              $pattern = array('  ', ',');
              $replace = array('', '');
              $new_data[$record_id][] = str_replace($pattern, $replace, trim($file_row[$mapping_key]));
              break;
          }
        }
      }
    }
    return $new_data;
  }

  /**
   * Scan directory for ing csv export files
   * @param $dir
   * @return array
   */
  public function get_files_from_dir($dir) {
    $files = array();
    foreach ($dir as $fileinfo) {
      if (!$fileinfo->isDot()) {
        if ($fileinfo->getExtension() == 'csv') {
          $files[] = $fileinfo->getPathname();
        }
      }
    }
    return $files;
  }
}