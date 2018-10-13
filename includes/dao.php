<?php

class DAO {

  protected $db;

  public function setDb($db)
  {
      $this->db = $db;
  }



  function read_albums() {
    try {
      $id = 0;
      $query = $this->db->prepare('SELECT * FROM pitchfork_albums where id > :id order by review_date desc, id desc LIMIT 0,100');
      $query->execute( array(':id' => $id) );
      $rows = [];
      while ($row = $query->fetchObject()) {
         $rows[] = $row;
      }
      return $rows;
    }
    catch(PDOException $e) {
      //echo "Error: " . $e->getMessage();
      return false;
    }
  }

  function filter_albums($genre, $album_year, $score) {
    $where_clause = "";

    if ($genre != "") {
			$where_clause .= "genre = :genre and ";
		}
    if ($album_year != "") {
			$where_clause .= "album_year LIKE :album_year and ";
		}
    if ($score != "") {
			$where_clause .= "(score LIKE :score) and ";
		}
    try {
      $query = $this->db->prepare("SELECT * FROM pitchfork_albums WHERE ".$where_clause." id > 0 order by review_date desc, id desc LIMIT 0,100");

      if ($genre != "") {
				$query->bindParam(':genre', $genre, PDO::PARAM_STR);
			}
      if ($album_year != "") {
					$query->bindParam(':album_year', $tempString = "%".$album_year, PDO::PARAM_STR);
			}
      if ($score != "") {
				$query->bindParam(':score', $tempString = $score."%", PDO::PARAM_STR);
			}

      $query->execute();
      $rows = [];
      while ($row = $query->fetchObject()) {
         $rows[] = $row;
      }
      return $rows;
    }
    catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
      return false;
    }
  }

  function read_genres() {
    try {
      $id = 0;
      $query = $this->db->prepare('SELECT DISTINCT genre FROM pitchfork_albums WHERE genre <> "" order by genre asc, id LIMIT 0,100');
      $query->execute();
      $rows = [];
      while ($row = $query->fetchObject()) {
         $rows[] = $row;
      }
      return $rows;
    }
    catch(PDOException $e) {
      //echo "Error: " . $e->getMessage();
      return false;
    }
  }

  function read_years() {
    try {
      $id = 0;
      $query = $this->db->prepare('SELECT DISTINCT album_year FROM pitchfork_albums WHERE album_year <> "" order by album_year desc, id LIMIT 0,200');
      $query->execute();
      $rows = [];
      while ($row = $query->fetchObject()) {
         $rows[] = $row;
      }
      return $rows;
    }
    catch(PDOException $e) {
      //echo "Error: " . $e->getMessage();
      return false;
    }
  }


}


?>
