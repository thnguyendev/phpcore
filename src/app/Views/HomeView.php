<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PHPWebCore</title>
</head>
<body>
    <h1>Hi <?php echo isset($args["name"]) ? $args["name"] : "there" ?>, welcome to PHPWebCore!</h1>
</body>
</html>