<?php 
require_once('config.php');
$sql_query = "SELECT * FROM enquires order by id desc";
$resultset = mysqli_query($conn, $sql_query) or die("database error:". mysqli_error($conn));
$tasks = array();
while( $rows = mysqli_fetch_assoc($resultset) ) {
  $tasks[] = $rows;
}

$filename = "enquires_".date('Ymd') . ".xls";     
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
ExportFile($tasks);
exit();
function ExportFile($records) {
  $heading = false;
    if(!empty($records))
      foreach($records as $row) {
      if(!$heading) {
        // display field/column names as a first row
        echo implode("\t", array_keys($row)) . "\n";
        $heading = true;
      }
      echo implode("\t", array_values($row)) . "\n";
      }
    exit;
}