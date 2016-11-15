<?php
  // Sets the time for the session and then starts it.
  session_set_cookie_params($session_time);
  session_name($session_name);
  session_start();
  session_regenerate_id(true);
?>
