<?php


namespace Controllers;

use Delight\Auth\Auth;
use Models\ImageManager;
use League\Plates\Engine;
use Models\QueryBuilder;
use SimpleMail;
use Tamtamchik\SimpleFlash\Flash;

class Profile
{
    private $templates;
    private $auth;
    private $mailer;
    private $db_pdo;

    public function __construct( Engine $engine, QueryBuilder $queryBuilder, Auth $auth, SimpleMail $simpleMail )
    {
        $this -> auth = $auth;
        $this -> db_pdo = $queryBuilder;

        $this -> mailer = $simpleMail;
        $this -> templates = $engine;
    }

    public function index()
    {
        // Render a template
        echo $this -> templates -> render( 'profile.view', [ '' => null ] );
    }

    public function profile_change()
    {
        if ($_FILES['image']['error'] == 0) {
            $_SESSION['image_name'] = $_FILES['image']['name'];
            $_SESSION['image_tmp_name'] = $_FILES['image']['tmp_name'];

            //Название до точки переименовывается в уникальное имя, затем отдельно к имени конкатенируется расширение
            $image_name = ImageManager ::getImageNameWithoutExtension( $_SESSION['image_name'] );
            $image_name_ext = ImageManager ::getImageExtensionWithoutName( $_SESSION['image_name'] );
            $uniq_image_name = ImageManager ::getUniqImageName( $image_name );
            $full_uniq_image_name = ImageManager ::getFullUniqImageFileName( $uniq_image_name, $image_name_ext );

            //Получение пути к изображению из БД для удаления картинки из папки на сервере
            $get_avatar_path = $this -> db_pdo -> getImagePath( 'users', $_SESSION['auth_user_id'] );

            if (isset( $get_avatar_path )) {
                //Удаление существующего файла изображения
                $image_file_location = __DIR__ . "/../" . $get_avatar_path;
                if (ImageManager ::validateImageLocation( $image_file_location )) {
                    ImageManager ::deleteImageFile( $image_file_location );
                }
            }

            //Перемещает загруженный файл в необходимую папку по полному пути проекта
            $image_path_DB = __DIR__ . "/../public/avatar/";
            ImageManager ::moveUploadedImageFile( $_SESSION['image_tmp_name'],
                $image_path_DB . $full_uniq_image_name );

            //Путь на локальном сервере к изображению аватара, передается дальше в БД
            //Выполняется обрезание части полного пути к файлу до названия папки с изображением
            $shorted_path = $image_path_DB . $full_uniq_image_name;
            $cutImg_path = ImageManager ::cutImagePathForDB( $shorted_path, "public" );
            $_SESSION['image_dir'] = $cutImg_path;

            //Отображение изобрадения аватарки после изменения изобрадения
            $_SESSION['avatar_image'] = $_SESSION['image_dir'];

            //Send avatar's path to DB into image table
            $this -> db_pdo -> sendImagePath( 'users', $_SESSION['image_dir'], $_SESSION['auth_user_id'] );

        }

        if ($this -> auth -> isLoggedIn()) {

            try {

                $this -> auth -> changeEmail( $_POST['newEmail'], function ( $selector, $token ) {
                    $fromEmail = 'post.project@mail.net';
                    $fromName = 'Admin';
                    $subject = 'Подтвердите вашу почту';
                    $url = '<a href="http://components/profile_change_email?selector=' . \urlencode( $selector ) . '&token=' . \urlencode( $token ) . '">Подвердите новый e-mail адрес</a>';
                    $message = $url;

                    SimpleMail ::make()
                        -> setTo( $_SESSION['auth_email'], $_SESSION['auth_username'] )
                        -> setFrom( $fromEmail, $fromName )
                        -> setSubject( $subject )
                        -> setMessage( $message )
                        -> setHtml()
                        -> send();
                } );
                $_SESSION['newEmail'] = $_POST['newEmail'];
                Flash ::message( 'Проверьте вашу почту и подтвердите изменение адреса', 'info' );
                header( "Location: /profile" );

            } catch ( \Delight\Auth\InvalidEmailException $e ) {
                Flash ::message( ' Невалидный e-mail адрес  ', 'warning' );
                header( "Location: /profile" );
            } catch ( \Delight\Auth\UserAlreadyExistsException $e ) {
                Flash ::message( ' E-mail адрес не изменен  ', 'info' );
                header( "Location: /profile" );
            } catch ( \Delight\Auth\EmailNotVerifiedException $e ) {
                Flash ::message( ' E-mail адрес не подтвержден  ', 'warning' );
                header( "Location: /profile" );
            } catch ( \Delight\Auth\NotLoggedInException $e ) {
                Flash ::message( ' Отсутствует авторизация  ', 'warning' );
                header( "Location: /profile" );
            } catch ( \Delight\Auth\TooManyRequestsException $e ) {
                Flash ::message( ' Много неудачных попыток, попробуйте позже  ', 'error' );
                header( "Location: /profile" );
            }
        }
    }

    public function change_password()
    {
        if (isset( $_POST['current'] ) or isset( $_POST['password'] ) or $_POST['password_confirmation']) {
            //Array values for password input fields
            $password = [
                'current'               => $_POST['current'],
                'password'              => $_POST['password'],
                'password_confirmation' => $_POST['password_confirmation'],
                '$id_fromDB'            => $_SESSION['auth_user_id']
            ];
        }

        //Below all input password fields validation
        if (!empty( $password['current'] )) {
            if (!empty( $password['current'] )) {
                if (strlen( $_POST['current'] ) < 6) {
                    $_SESSION['login_password_change'] = 'Введенный пароль для проверки слишком короткий';
                    goto end;
                }
            }
        }

        if (isset( $password['password'] )) {
            if (!empty( $password['password'] )) {
                if (strlen( $password['password'] ) < 6) {
                    $_SESSION['password_change'] = 'Пароль должен быть больше 6-ти символов';
                    goto end;
                }
                if (ctype_space( $password['password'] )) {
                    $_SESSION['password_change'] = 'Введен некорректный пароль';
                    goto end;
                }
                if ($password['password'] != $password['password_confirmation']) {
                    $_SESSION['password_change'] = 'Новый пароль не совпадает';
                    goto end;
                }

            } else {
                if (empty( $password['password_confirmation'] )) {
                    $_SESSION['password_change'] = 'Введите новый пароль';
                    goto end;
                }
            }
        }

        try {
            $this -> auth -> changePassword( $password['current'], $password['password'] );

            $_SESSION['message_done'] = 'Пароль успешно изменен';
            header( "Location: /profile" );
        } catch ( \Delight\Auth\NotLoggedInException $e ) {
            Flash ::message( ' Отсутствует авторизация  ', 'warning' );
            header( "Location: /profile" );
            die( 'Not logged in' );
        } catch ( \Delight\Auth\InvalidPasswordException $e ) {
            $_SESSION['login_password_change'] = 'Введен неверный  пароль';
            header( "Location: /profile" );
        } catch ( \Delight\Auth\TooManyRequestsException $e ) {
            Flash ::message( ' Много неудачных попыток, попробуйте позже  ', 'error' );
            header( "Location: /profile" );
        }
        end:
        header( "Location: /profile" );
    }

    public function change_email()
    {
        try {
            $this -> auth -> confirmEmail( $_GET['selector'], $_GET['token'] );

            Flash ::message( 'Ваш e-mail успешно изменен', 'success' );
            header( "Location: /profile" );
        } catch ( \Delight\Auth\InvalidSelectorTokenPairException $e ) {
            Flash ::message( 'Неверный токен', 'info' );
            header( "Location: /profile" );
        } catch ( \Delight\Auth\TokenExpiredException $e ) {
            Flash ::message( 'Время использования токена просрочено', 'info' );
            header( "Location: /profile" );
        } catch ( \Delight\Auth\UserAlreadyExistsException $e ) {
            Flash ::message( 'E-mail адрес уже существует', 'warning' );
            header( "Location: /profile" );
        } catch ( \Delight\Auth\TooManyRequestsException $e ) {
            Flash ::message( ' Много неудачных попыток, попробуйте позже  ', 'error' );
            header( "Location: /profile" );
        }
    }
}