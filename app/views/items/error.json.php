<?php
header("Content-Type: application/json; charset=utf-8");
?>
{
    "error":
    {
        "code":"<?php echo $error_code; ?>",
        "message":"<?php echo $error_message; ?>"
    }
}