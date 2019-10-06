<?php $this -> layout( 'layout', [ 'title' => 'Главная страница' ] ); ?>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                Posts Project
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                </ul>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <?php if (isset( $_SESSION['auth_username'] )) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"
                               role="button"><?= $_SESSION['auth_username']; ?></a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="/profile">Профиль</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/logout">Выход</a>
                            </div>
                        </li>
                    <? } else { ?>
                        <!-- Authentication Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/registration">Register</a>
                        </li>
                    <? } ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><h3>Комментарии</h3></div>
                        <div class="card-body">
                            <!-- Success flash message -->
                            <?php if ( isset( $_SESSION['message'] ) ) : ?>
                            <div class="alert alert-success" role="alert">
                                <? echo $_SESSION['message'];
                                endif;
                                unset( $_SESSION['message'] ); ?>
                                <!-- Danger flash message -->
                                <?php if ( isset ( $_SESSION['message_danger'] ) ) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <? echo $_SESSION['message_danger'];
                                    endif;
                                    unset( $_SESSION['message_danger'] ); ?>
                                </div>
                                <!-- Loop shows all comments, using pagination filter -->
                                <?php foreach ( $paginator_items as $comment ): ?>
                                    <div class="media">
                                        <img src="
                                        <? if (is_file( __DIR__ . "/../" . $this -> e( $comment['image'] ) ) && file_exists( __DIR__ . "/../" . $this -> e( $comment['image'] ) )) {
                                            echo $this -> e( $comment["image"] );
                                        } else {
                                            echo '../public/img/no-user.jpg';
                                        } ?>" class="mr-3" alt="..." width="64" height="64">
                                        <div class="media-body">
                                            <h5 class="mt-0"><?= $this -> e( $comment["user"] ); ?></h5>
                                            <span><small><?= $this -> e( $comment["date"] ); ?></small></span>
                                            <p>
                                                <?= $this -> e( $comment["text"] ); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!--Warning message about authorisation need before comment-->
                            <?php if ( !$_SESSION or !isset( $_COOKIE ) ) { ?>
                            <div class="alert alert-primary" style="margin-top: 20px; role=" alert
                            ">
                            Чтобы оставить комментарий <a href="/login" class="href">авторизуйтесь</a>
                        </div>
                        <?php } ?>
                    </div>
                    <!--Custom Pagination is here-->
                    <ul class="pagination" style="margin-top: 15px;">
                        <?php if ($paginator->getPrevUrl()): ?>
                            <li><a href="<?php echo $paginator->getPrevUrl(); ?>">&laquo; Первая  &nbsp</a></li>
                        <?php endif; ?>
                        <?php foreach ($paginator->getPages() as $page): ?>
                            <?php if ($page['url']): ?>
                                <li <?php echo $page['isCurrent'] ? 'class="active"' : ''; ?>>
                                    <a href="<?php echo $page['url'] ; ?>"><?php echo $page['num'] . "&nbsp" ; ?></a>
                                </li>
                            <?php else: ?>
                                <li class="disabled"><span><?php echo $page['num'] ; ?></span></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($paginator->getNextUrl()): ?>
                            <li><a href="<?php echo $paginator->getNextUrl(); ?>">Последняя &raquo; &nbsp</a></li>
                        <?php endif; ?>
                    </ul>
                    <div class="col-md-12" style="text-align: center">Всего постов <?php echo $paginator -> getTotalItems(); ?>,
                        отображается <?php echo $paginator -> getCurrentPageFirstItem(); ?> -
                        <?php echo $paginator -> getCurrentPageLastItem(); ?> пост
                    </div> <!--Custom Pagination ends here-->

                    <div class="col-md-12" style="margin-top: 4px;">
                        <div class="card">
                            <div class="card-header"><h3>Оставить комментарий</h3></div>
                            <div class="card-body">
                                <form action="/add_post" method="post">
                                    <!--User name for comment-->
                                    <?php if (isset( $_SESSION['auth_username'] )) { ?>
                                        <div class="form-group">
                                            <input type="hidden" name="name" value="<?= $_SESSION['auth_username']; ?>">
                                        </div>
                                    <? } else { ?>
                                        <!--User name for comment if no session data exist-->
                                        <div class="form-group">
                                            <label for="exampleFormControlTextarea1">Имя</label>
                                            <input name="name" class="form-control" id="exampleFormControlTextarea1"/>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label for="exampleFormControlTextarea1">Сообщение</label>
                                        <textarea name="text" class="form-control" id="exampleFormControlTextarea1"
                                                  rows="3"></textarea>
                                    </div>
                                    <?php if (isset( $_SESSION['auth_username'] )) { ?>
                                        <button type="submit" class="btn btn-success">Отправить</button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-danger"
                                                onclick="window.location.href='/login'">Авторизуйтесь
                                        </button>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>