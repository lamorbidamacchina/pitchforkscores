<?php
// TODO: motore di ricerca libera, motore di ricerca filtri, inserire dati mancanti per i primi 20, paginazione
require_once("./includes/config.php");
include_once("./includes/dao.php");
$dao = new DAO();
$dao->setDb($db);

$genre_post = urldecode(filter_var($_GET['genre'],FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES));
$album_year = filter_var($_GET['album_year'],FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
$score_post = filter_var($_GET['score'],FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
$submit = filter_var($_GET['submit'],FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
if ($submit == "filter") {
	$rows = $dao->filter_albums($genre_post,$album_year,$score_post);
}
else {
	$rows = $dao->read_albums();
}


$genres = $dao->read_genres();
$years = $dao->read_years();
//var_dump($rows);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Pitchfork's Album review scores</title>
	<?php include_once('./includes/head.php');?>
</head>
<body class="hp">
  <h1>
    Pitchfork's scores
    <div class="since">(Albums, since August, 2018)</div>
  </h1>

  <div class="container">
		<div id="search">
			<form id="f1" name="f1" method="GET" >
				<div class="row">
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<select name="genre" id="genre">
							<option value="">Choose genre</option>
							<option value="">---</option>
							<option value="">All</option>
							<?php foreach($genres as $genre) { ?>
							<option value="<?php echo urlencode($genre->genre);?>" <?php if ($genre_post == $genre->genre) {echo 'selected="selected"';}?>><?php echo $genre->genre;?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<select name="album_year" id="album_year">
							<option value="">Choose year</option>
							<option value="">---</option>
							<?php foreach($years as $year) {
								$clean_year = str_replace("  •   ","",$year->album_year);
								$clean_year = str_replace("•   ","",$clean_year);
								$clean_year = str_replace("• ","",$clean_year);
								$clean_year = trim($clean_year);
								?>
							<option value="<?php echo urlencode($clean_year);?>" <?php if ($album_year == $clean_year) {echo 'selected="selected"';}?>><?php echo $clean_year;?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<select name="score" id="score">
							<option value="">Choose score</option>
							<option value="">---</option>
							<?php
							$scores = array(1,2,3,4,5,6,7,8,9,10);
							foreach ($scores as $score) { ?>
								<option value="<?php echo urlencode($score);?>" <?php if ($score_post == $score) {echo 'selected="selected"';}?>><?php echo $score." and something";?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<button type="submit" name="submit" value="filter" id="submit">Filter</button>
					</div>
				</div>

			</form>
		</div>
    <table class="minimal" id="scores">
      <thead>
        <tr>
          <th>Date</th>
          <th>Cover</th>
          <th>Score</th>
          <th>Author</th>
          <th>Album</th>
          <th>Genre</th>
          <th>Label</th>
          <th>Year</th>
        </tr>
      </thead>

    </thead>
    <tbody>
      <?php foreach($rows as $item) { ?>
        <tr class="row_<?php echo $item->id;?>">
          <td>
          <?php
            //echo $item->pubdate;
            $time = strtotime($item->review_date);
            $newformat = date('d M Y',$time);
            echo $newformat;
          ?>
          </td>
          <td>
            <a href="<?php echo $item->link; ?>" target="_blank" title="<?php echo $item->description; ?>">
              <img src="<?php echo $item->album_art; ?>" width="80"/>
            </a>
          </td>
          <?php
            $more_than_80 = "";
            $score_num = str_replace(".","",$item->score);
            if ($score_num > 80) {$more_than_80 = "circle_red";}
          ?>
          <td><div class="circle <?php echo $more_than_80;?>"><?php echo $item->score; ?></div></td>
          <td><?php echo $item->album_author; ?></td>
          <td><?php echo $item->album_title; ?></td>
          <td><?php echo $item->genre; ?></td>
          <td><?php echo $item->album_label; ?></td>
          <td><?php echo str_replace("• ","",$item->album_year); ?></td>
        </tr>
      <?php }?>
    </tbody>
  </table>
  </div>
</body>
</html>
