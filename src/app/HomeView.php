<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PHPCore</title>
</head>
<body>
    <h1>Hi <?php echo isset($this->parameters["name"]) ? $this->parameters["name"] : "there"; ?>! Welcome to PHPCore.</h1>
</body>
</html>