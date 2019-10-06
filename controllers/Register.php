<?php


namespace Controllers;

use Delight\Auth\Auth;
use League\Plates\Engine;
use Models\QueryBuilder;
use SimpleMail;

class Register
{
    private $db;
    private $auth;
    private $mailer;
    private $templates;

    public function __construct( QueryBuilder $queryBuilder, Auth $auth, SimpleMail $simpleMail, Engine $engine )
    {
        $this -> db = $queryBuilder;
        $this -> auth = $auth;
        $this -> mailer = $simpleMail;
        $this -> templates = $engine;
    }

    public function index()
    {
        // Render a template
        echo $this -> templates -> render( 'register.view', [ '' => null ] );
    }

    public function registration()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirmation'];
        $email_validate = filter_var( "$email", FILTER_VALIDATE_EMAIL );

        if (ctype_space( $name )) {
            $_SESSION['message_name'] = 'Введенно некорректное имя';
            goto end;
        }

        if (!empty( $email )) {
            if ($email_validate == false) {
                $_SESSION['message_email'] = 'Введен некорректный формат e-mail';
                goto end;
            }
        }

        if (ctype_space( $password )) {
            $_SESSION['message_password'] = 'Введен некорректный пароль';
            goto end;
        }

        if (!empty( $password )) {
            if (strlen( $password ) < 6) {
                $_SESSION['message_password'] = 'Пароль должен быть больше 6-ти символов';
                goto end;
            }
        }
        if ($password != $password_confirm) {
            $_SESSION['message_password'] = 'Пароль не совпадает';
            goto end;
        }

        try {
            $userId = $this -> auth -> register( $_POST['email'], $_POST['password'], $_POST['username'], function ( $selector, $token ) {

                $fromEmail = 'post.project@mail.net';
                $fromName = 'Admin';
                $subject = 'Подтвердите вашу почту';
                $url = '<a href="http://components/verification_email?selector=' . \urlencode( $selector ) . '&token=' . \urlencode( $token ) . '">Подвердите Вашу регистрацию</a>';
                $message = $url;

                SimpleMail ::make()
                    -> setTo( $_POST['email'], $_POST['username'] )
                    -> setFrom( $fromEmail, $fromName )
                    -> setSubject( $subject )
                    -> setMessage( $message )
                    -> setHtml()
                    -> send();
            } );

            $_SESSION['login_email_success'] = 'Проверьте вашу почту для завершения регистрации';
            header( "Location: /login" );

        } catch ( \Delight\Auth\InvalidEmailException $e ) {
            $_SESSION['message_email'] = 'Неверный формат e-mail';
        } catch ( \Delight\Auth\InvalidPasswordException $e ) {
            $_SESSION['message_password'] = 'Неверный пароль';
        } catch ( \Delight\Auth\UserAlreadyExistsException $e ) {
            $_SESSION['message_email'] = 'E-mail адрес уже зарегистрирован';
        } catch ( \Delight\Auth\TooManyRequestsException $e ) {
            $_SESSION['message_email'] = 'Много неудачных попыток, попробуйте позже';
        }

        end:
        echo $this -> templates -> render( 'register.view', [ 'auth' => $this -> auth ] );
    }

    public function email_validation()
    {
        try {
            $this -> auth -> confirmEmail( $_GET['selector'], $_GET['token'] );

            $_SESSION['login_email_success'] = 'E-mail подтвержден, используйте его для входа';
            header( "Location: /login" );
        } catch ( \Delight\Auth\InvalidSelectorTokenPairException $e ) {
            $_SESSION['message_email'] = 'Неверный токен';
        } catch ( \Delight\Auth\TokenExpiredException $e ) {
            $_SESSION['message_email'] = 'Невалидный токен';
        } catch ( \Delight\Auth\UserAlreadyExistsException $e ) {
            $_SESSION['message_email'] = 'E-mail адрес уже существует';
        } catch ( \Delight\Auth\TooManyRequestsException $e ) {
            $_SESSION['message_email'] = 'Много неудачных попыток, попробуйте позже';
        }
    }
}