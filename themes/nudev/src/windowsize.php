<?php

  session_start();

  $_SESSION['windowsize'] = array('height' => $_POST['height'], 'width' => $_POST['width']);

  // let JS know that we updated the values
  echo 'window size updated';

?>
