<?php
  header("Content-Type: application/json; charset=utf-8");
?>

<?php var_dump(json_encode($item->hello())); ?>

{
    "action": "<?php echo $action_name; ?>",
    "word":   "<?php echo $item->hello(); ?>",
    "item": <?php json_encode($item_info); ?> 

}