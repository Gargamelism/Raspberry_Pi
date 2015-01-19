<html>
<?php

function clear_states() {
  $db = mysqli_connect('localhost', 'switcher', '12345', 'matrix_control');
  if($db->connect_errno > 0) {
    echo('Unable to connect to database [' . $db->connect_error . ']');
  }

  // clear led matrix
  for($i = 1; $i < 65; $i++) {
    $query = "UPDATE led_matrix SET state = 0 WHERE id = $i";
    $result = mysqli_query($db, $query);
    if(!$result) {
      echo "could not clear the leds";
    }
  }

  // clear flow and flicker
  $query = "UPDATE led_flow SET state = 0 WHERE id = 'flow'";
  $result = mysqli_query($db, $query);
  if(!$result) {
    echo "could not set flow";
  }
  $query = "UPDATE led_flow SET state = 0 WHERE id = 'flicker'";
  $result = mysqli_query($db, $query);
  if(!$result) {
    echo "could not set flicker";
  }
  
}

function switch_db() {
  $db = mysqli_connect('localhost', 'switcher', '12345', 'matrix_control');
  if($db->connect_errno > 0) {
    echo('Unable to connect to database [' . $db->connect_error . ']');
  }

  clear_states();

  // check each value from post and if it is an index or a flow, change the db
  foreach($_POST as $key => $val) {
    if((intval($key) > 0) and (intval($key) < 65)) {
      $query = "UPDATE led_matrix SET state = 1 WHERE id = $key";
      $result = mysqli_query($db, $query);
      if(!$result) {
        echo "could not set the leds";
        echo "$query";
      }
    }
    elseif(!strcmp($key, "flow")) {
      $query = "UPDATE led_flow SET state = 1 WHERE id = '$key'";
      $result = mysqli_query($db, $query);
      if(!$result) {
        echo "could not set flow";
      }
    }
    elseif(!strcmp($key, "flicker")) {
      $query = "UPDATE led_flow SET state = 1 WHERE id = '$key'";
      $result = mysqli_query($db, $query);
      if(!$result) {
        echo "could not set flicker";
      }
    }
  }

  $result->free();
  $db->close();

  header("Location: http://mapi.com");
}


if(isset($_POST['fuzzy']))
  switch_db();
else
  echo "no post!";
  ?>
</html>
