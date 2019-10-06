<?php


namespace Controllers;

use Models\QueryBuilder;

class Comments
{
    private $db;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this -> db = $queryBuilder;
    }

    public function addPost()
    {
        $this -> db -> addPost( 'posts',
            [
                'user'    => $_POST['name'],
                'text'    => $_POST['text'],
                'image'   => 'img/no-user.jpg',
                'date'    => date( 'd/m/Y' ),
                'hidden'  => '0',
                'user_id' => $_SESSION['auth_user_id']
            ] );
        header( "location: /" );
    }
}