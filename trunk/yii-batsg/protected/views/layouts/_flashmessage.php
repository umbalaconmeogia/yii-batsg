<?php
  // $key is "success", "notice" or "error".
  foreach (Yii::app()->user->getFlashes() as $key => $message) {
    if ($key != 'counters') {
      echo "<div class=\"flash-{$key}\">{$message}</div>\n";
    }
  }
?>