<?php 
require_once("./includes/config.php");
include_once("./includes/dao.php");
$dao = new DAO();
$dao->setDb($db);

$rows = $dao->read_all_albums();
$data = "review_date;score;author;title;genre;label;year;url;\n";
foreach($rows as $item) {
  $review_date = strtotime($item->review_date);
  $review_date = date('Y-m-d',$review_date);
  $data .= $review_date.';'.$item->score.';"'.$item->album_author.'";"'.$item->album_title.'";"'.$item->genre.'";"'.$item->album_label.'";'.trim(str_replace("• ","",$item->album_year)).';"'.$item->link.'";'."\n";
}

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=pitchfork-scores-export.csv");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";
?>