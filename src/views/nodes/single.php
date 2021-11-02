<?php
use Replicant\Log;
use Replicant\Tables\Nodes\Functions;

$item = Functions::get( $id );
?>

<div class="wrap">

   <div class="replicant-box">
      <h2 class="replicant-heading"><?php esc_html_e("Information", "replicant") ?></h2>
      <ul class="replicant-node-information">
         <li>
            <span class="node-information-head"><?php esc_html_e("id", "replicant") ?></span>
            <span class="node-information-value"><?php echo $item->id ?></span>
         </li>
         <li>
            <span class="node-information-head"><?php esc_html_e("name", "replicant") ?></span>
            <span class="node-information-value"><?php echo $item->name ?></span>
         </li>
         <li>
            <span class="node-information-head"><?php esc_html_e("host", "replicant") ?></span>
            <span class="node-information-value"><?php echo $item->host ?></span>
         </li>
         <li>
            <span class="node-information-head"><?php esc_html_e("port", "replicant") ?></span>
            <span class="node-information-value"><?php echo $item->port ?></span>
         </li>
      </ul>
   </div>

   <?php
   // Fetch all logs related to current node
   $node_logs = Log::get_all($item->id, ARRAY_A);
   ?>
   <div class="replicant-logs-container replicant-box">
      <h2 class="replicant-heading"><?php esc_html_e("Logs", "replicant") ?></h2>
      <?php if(empty($node_logs)): ?>
      <p class="replicant-text`-danger"><?php esc_html_e("No logs found.", "replicant") ?></p>
      <?php else: ?>
      <ul>
      <?php foreach($node_logs as $log): ?>
         <?php
         $timestamp = strtotime(esc_html($log["created_at"]));
         $human_readable_time = human_time_diff($timestamp, current_time( 'U' ));
         $human_readable_level = Log::human_readable_level();
         ?>
         <li>
            <span class="log-id">#<?php echo intval($log["id"]) ?></span>
            <span class="log-level log-level-<?php echo esc_html($log["level"]) ?> font-weight-bold"><?php echo esc_html($human_readable_level) ?></span>
            <span class="log-message"><?php echo esc_html($log["message"]) ?></span>
            <span class="log-date"><?php echo esc_html($human_readable_time) . " " . esc_html__("ago", "replicant") ?></span>
         </li>
      <?php endforeach; ?>
      </ul>
      <?php endif; ?>
   </div>
</div>

