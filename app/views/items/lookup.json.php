<?php
header("Content-Type: application/json; charset=utf-8");
?>
{
    "action": "<?php echo $action_name; ?>",
    "word":   "<?php echo $item->moe(); ?>"
}