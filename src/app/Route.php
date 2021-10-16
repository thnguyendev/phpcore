<?php
namespace App;

use PHPCore\AppRoute;

class Route extends AppRoute
{
    public function initialize()
    {
        $this->mapping = 
        [
            "/" => [],
            "product" => ["params" => ["group", "name"]],
            "product/details" => ["params" => ["id"]]
        ];
    }
}
?>