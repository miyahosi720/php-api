<?php
header("Content-Type: application/json; charset=utf-8");
?>
{
    "result":
        {
        "requested":
            {
            "parameter":<?php echo json_encode($parameter); ?>,
            "timestamp":<?php echo $timestamp; ?>,
            },
        "item_count":<?php echo $item_count; ?>,
        "item":<?php echo json_encode($item) . "\n"; ?>
        }
}