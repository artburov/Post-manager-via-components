<?php


namespace Controllers;

use Models\QueryBuilder;
use League\Plates\Engine;
use JasonGrimes\Paginator;

class Homepage
{
    private $db;
    private $templates;

    public function __construct( Engine $engine, QueryBuilder $queryBuilder )
    {
        $this -> db = $queryBuilder;

        // Create new Plates instance
        $this -> templates = $engine;
    }

    public function index()
    {
        $posts = $this -> db -> getAllPosts( 'posts' );

        //Paginator filter from DB
        $paginator_items = $this -> db -> getPaginationPosts( 'posts' );

        $totalItems = count( $posts );
        $itemsPerPage = 5;
        $currentPage = $_GET['page'] ?? 1;
        $urlPattern = '?page=(:num)';

        $paginator = new Paginator( $totalItems, $itemsPerPage, $currentPage, $urlPattern );

        //Получение пути к изображению аватарки из БД для вьюшки профайла
        $get_avatar_path = $this -> db -> getImagePath( 'users', $_SESSION['auth_user_id'] );

        //Сессия для отображения аватарки в профиле
        $_SESSION['avatar_image'] = $get_avatar_path;

        // Render a template
        echo $this -> templates -> render( 'main.view', [ 'comments' => $posts, 'paginator' => $paginator, 'paginator_items' => $paginator_items ] );
    }
}
