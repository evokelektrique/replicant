<?php
use Replicant\Log;
use Replicant\Tables\Nodes\Functions;

$item = Functions::get( $id );
?>

<div class="wrap">
   <h2>Information</h2>
   <ul>
      <li>ID: <b><?= $item->id ?></b></li>
      <li>NAME: <b><?= $item->name ?></b></li>
      <li>HOST NAME: <b><?= $item->host ?></b></li>
      <li>PORT: <b><?= $item->port ?></b></li>
   </ul>

   <?php
   // Fetch all logs related to current node
   $node_logs = Log::get_all($item->id, ARRAY_A);
   ?>
   <h2>Logs</h2>
   <div class="replicant-logs-container">
   <?php if(empty($node_logs)): ?>
      <p class="replicant-text`-danger">No logs found.</p>
   <?php else: ?>
      <ul>
      <?php foreach($node_logs as $log): ?>
         <?php
         $timestamp = strtotime($log["created_at"]);
         $human_readable_time = human_time_diff($timestamp, current_time( 'U' ));
         $human_readable_level = Log::human_readable_level($log["level"]);
         ?>
         <li>
            <span>ID: <?= $log["id"] ?></span>
            <span><?= $human_readable_level ?></span>
            <span><?= $log["message"] ?></span>
            <span><?= $human_readable_time . " " . __("ago", "replicant") ?></span>
         </li>
      <?php endforeach; ?>
      </ul>
   <?php endif; ?>
   </div>
</div>

