<?php

namespace models;

use core\Database;

class Category
{
    public static function all()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
