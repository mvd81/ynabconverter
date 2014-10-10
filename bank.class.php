<?php
namespace bankClass;

interface bank {
  public function set_mapping($mapping = FALSE);
  public function get_mapping();
  public function get_bank_data($data);
  public function get_files_from_dir($dir);
}