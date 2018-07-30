<?php
header("HTTP/1.1 301 Moved Permanently", true, 301);
header("Location: " . get_admin_url());
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <title>301 Moved</title>
    </head>
    <body>
        <h1>301 Moved</h1>
        The document has moved <a href="<?php echo get_admin_url(); ?>">here</a>.
    </body>
</html>
