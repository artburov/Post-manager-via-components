<?php


namespace Controllers;

use Models\QueryBuilder;
use League\Plates\Engine;
use Faker\Factory;


class Admin
{
    private $db;
    private $templates;

    public function __construct(Engine $engine, QueryBuilder $queryBuilder)
    {
        $this -> db = $queryBuilder;

        // Create new Plates instance
        $this -> templates = $engine;
    }

    public function index()
    {

        $all_posts = $this -> db -> adminGetAllPosts( 'posts' );
        echo $this -> templates -> render( 'admin.view', [ 'posts' => $all_posts ] );
    }

    public function post_deny()
    {
        $post_text = $_POST['post_text'];
        $deny = $_POST['deny'];

        $this -> db -> adminDenyPost( 'posts', $post_text, $deny );
        header( "Location: /admin" );
    }

    public function post_allow()
    {
        $post_text = $_POST['post_text'];
        $allow = $_POST['allow'];

        $this -> db -> adminAllowPost( 'posts', $post_text, $allow );
        header( "Location: /admin" );
    }

    public function post_delete()
    {
        $post_text = $_POST['post_text'];
        $allow = $_POST['allow'];

        $this -> db -> adminDeletePost( 'posts', $post_text );
        header( "Location: /admin" );
    }

    public function faker()
    {
        $faker = Factory ::create();

        for ( $i = 0; $i < 10; $i++ ) {
            $this -> db -> addFakePosts( 'posts', $faker -> text, $faker -> name, $faker -> date( "m/d/Y", "now" ) );
        }

        header( "Location: /" );
    }
}