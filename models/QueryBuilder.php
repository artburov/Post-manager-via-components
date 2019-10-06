<?php

namespace Models;

use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder
{
    private $pdo;
    private $queryFactory;

    public function __construct( PDO $pdo, QueryFactory $queryFactory)
    {
        $this -> pdo = $pdo;
        $this->queryFactory = $queryFactory;
    }

    public function getAllPosts( $table )
    {
        $select = $this -> queryFactory -> newSelect();

        $select -> cols( [ '*' ] )
            -> from( $table )
            -> join( 'LEFT', 'users', "users.id = {$table}.user_id" )
            -> where( 'hidden = 0' )
            -> orderBy( [ "$table.id DESC" ] );

        $sth = $this -> pdo -> prepare( $select -> getStatement() );

        $sth -> execute( $select -> getBindValues() );

        $result = $sth -> fetchAll( PDO::FETCH_ASSOC );
        return $result;
    }

    public function getPaginationPosts( $table )
    {
        $select = $this -> queryFactory -> newSelect();
        $select -> cols( [ '*' ] )
            -> from( $table )
            -> join( 'LEFT', 'users', "users.id = {$table}.user_id" )
            -> where( 'hidden = 0' )
            -> setPaging(5)
            -> page($_GET['page'] ?? 1)
            -> orderBy( [ "$table.id DESC" ] );

        $sth = $this -> pdo -> prepare( $select -> getStatement() );

        $sth -> execute( $select -> getBindValues() );

        $items = $sth -> fetchAll( PDO::FETCH_ASSOC );
        return $items;
    }

    public function addPost( $table, $data )
    {
        $insert = $this -> queryFactory -> newInsert();

        $insert
            -> into( $table )
            -> cols( $data );

        $sth = $this -> pdo -> prepare( $insert -> getStatement() );

        $sth -> execute( $insert -> getBindValues() );
    }

    public function sendImagePath( $table, $img_dir, $id )
    {
        $update = $this -> queryFactory -> newUpdate();

        $update -> table( $table )
            -> cols( [ 'image' => $img_dir ] )
            -> where( 'id = :id' )
            -> bindValue( ':id', $id );

        $sth = $this -> pdo -> prepare( $update -> getStatement() );

        $sth -> execute( $update -> getBindValues() );
    }

    public function getImagePath( $table, $id )
    {
        $select = $this -> queryFactory -> newSelect();
        $select
            -> cols( [ '*' ] )
            -> from( $table )
            -> where( 'id = :id' )
            -> bindValue( ':id', $id );

        $sth = $this -> pdo -> prepare( $select -> getStatement() );

        $sth -> execute( $select -> getBindValues() );

        $result = $sth -> fetch( PDO::FETCH_ASSOC );
        return $result['image'];
    }

    public function adminGetAllPosts( $table )
    {
        $select = $this -> queryFactory -> newSelect();

        $select -> cols( [ '*' ] )
            -> from( $table )
            -> join( 'LEFT', 'users', "users.id = {$table}.user_id" )
            -> orderBy( [ "$table.id DESC" ] );

        $sth = $this -> pdo -> prepare( $select -> getStatement() );

        $sth -> execute( $select -> getBindValues() );

        $result = $sth -> fetchAll( PDO::FETCH_ASSOC );
        return $result;
    }

    public function adminDenyPost( $table, $post_text, $display_status )
    {
        $update = $this -> queryFactory -> newUpdate();

        $update -> table( $table )
            -> cols( [ 'hidden' => $display_status ] )
            -> where( 'text = :text' )
            -> bindValue( ':text', $post_text );

        $sth = $this -> pdo -> prepare( $update -> getStatement() );

        $sth -> execute( $update -> getBindValues() );
    }

    public function adminAllowPost( $table, $post_text, $display_status )
    {
        $update = $this -> queryFactory -> newUpdate();

        $update -> table( $table )
            -> cols( [ 'hidden' => $display_status ] )
            -> where( 'text = :text' )
            -> bindValue( ':text', $post_text );

        $sth = $this -> pdo -> prepare( $update -> getStatement() );

        $sth -> execute( $update -> getBindValues() );
    }

    public function adminDeletePost( $table, $post_text )
    {
        $delete = $this -> queryFactory -> newDelete();

        $delete
            -> from( $table )                   // FROM this table
            -> where( 'text = :text' )
            -> bindValue( ':text', $post_text );

        $sth = $this -> pdo -> prepare( $delete -> getStatement() );

        $sth -> execute( $delete -> getBindValues() );
    }

    public function addFakePosts( $table, $faker_text, $faker_user, $faker_date )
    {
        $insert = $this -> queryFactory -> newInsert();

        $insert
            -> into( $table )
            -> cols(
                [
                    'text' => $faker_text,
                    'user' => $faker_user,
                    'date' => $faker_date
                ] );
        $insert -> addRow();

        $sth = $this -> pdo -> prepare( $insert -> getStatement() );

        $sth -> execute( $insert -> getBindValues() );
    }
}