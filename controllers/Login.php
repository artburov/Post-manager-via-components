<?php


namespace Controllers;

use Delight\Auth\Auth;
use League\Plates\Engine;
use Models\QueryBuilder;

class Login
{
    private $db;
    private $auth;
    private $templates;

    public function __construct( QueryBuilder $queryBuilder, Auth $auth, Engine $engine )
    {
        $this -> db = $queryBuilder;
        $this -> auth = $auth;
        $this -> templates = $engine;
    }

    public function index()
    {
        // Render a template
        echo $this -> templates -> render( 'login.view', [ '' => null ] );
    }

    public function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $email_validate = filter_var( $email, FILTER_VALIDATE_EMAIL );

        if (!empty( $email )) {
            if ($email_validate == false) {
                $_SESSION['login_email'] = 'Введен некорректный формат e-mail';
                goto end;
            }
        }
        if (ctype_space( $password )) {
            $_SESSION['login_password'] = 'Введен некорректный пароль';
            goto end;
        }
        if (!empty( $password )) {
            if (strlen( $password ) < 6) {
                $_SESSION['login_password'] = 'Пароль должен быть больше 6-ти символов';
                goto end;
            }
        }

        if ($_POST['remember'] == 1) {
            // сессия на 3 дня
            $rememberDuration = (int)( 60 * 60 * 24 * 3 );
        } else {
            // do not keep logged in after session ends
            $rememberDuration = null;
        }

        try {
            $this -> auth -> login( $_POST['email'], $_POST['password'], $rememberDuration );

            $_SESSION['login_email'] = 'Авторизация выполнена';
            header( "Location: /" );

        } catch ( \Delight\Auth\InvalidEmailException $e ) {
            $_SESSION['login_email'] = 'E-mail отсутствует';

        } catch ( \Delight\Auth\InvalidPasswordException $e ) {
            $_SESSION['login_password'] = 'Неверный пароль';

        } catch ( \Delight\Auth\EmailNotVerifiedException $e ) {
            $_SESSION['login_email'] = 'E-mail не подтвержден';

        } catch ( \Delight\Auth\TooManyRequestsException $e ) {
            $_SESSION['login_email'] = 'Много неудачных попыток, попробуйте позже';
        }

        if ($this -> auth -> isLoggedIn()) {
            header( 'Location: /' );
        } else {
            $this -> auth = false;
        }

        end:
        echo $this -> templates -> render( 'login.view', [ 'auth' => $this -> auth ] );
    }

    public function logout()
    {
        try {
            $this -> auth -> logOutEverywhere();
        } catch ( \Delight\Auth\NotLoggedInException $e ) {
            $_SESSION['message'] = 'Отсутствует авторизация';
        }
        $this -> auth -> destroySession();
        header( 'Location: /' );
    }
}