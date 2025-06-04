<?php 
 
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open(__DIR__ . '/db_projeto.db');
    }
}

?>