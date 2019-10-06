<?php $this -> layout( 'layout', [ 'title' => 'Админ панель' ] ); ?>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                Project
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
                    <!-- Authentication Links -->
                    <?php if (isset( $_SESSION['auth_username'] )) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"
                               role="button"><?= $_SESSION['auth_username']; ?></a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="/profile">Профиль</a>
                                <a class="dropdown-item" href="/faker">Faker 10</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/logout">Выход</a>
                            </div>
                        </li>
                    <? } else { ?>
                        <!-- Authentication Links on Default-->
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register.php">Register</a>
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
                        <div class="card-header"><h3>Админ панель</h3></div>

                        <div class="card-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Аватар</th>
                                    <th>Имя</th>
                                    <th>Дата</th>
                                    <th>Комментарий</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ( $posts as $status ) { ?>
                                    <tr>
                                    <td>
                                        <img src="<? if (file_exists( __DIR__ . "/../" . $status["image"] )) {
                                            echo $status["image"];
                                        } else {
                                            echo "../img/no-user.jpg";
                                        } ?> " alt="" class="img-fluid" width="64"
                                             height="64">
                                    </td>
                                    <td><?= $status['user']; ?></td>
                                    <td><?= $status['date']; ?></td>
                                    <td><?= $status['text']; ?>
                                    </td>
                                    <td>
                                    <?php if ($status['hidden'] == 0) { ?>
                                        <form action="/admin_deny" method="post">
                                            <input type="hidden" name="post_text" value="<?= $status['text']; ?>">
                                            <button type="submit" name="deny" class="btn btn-warning" value="1">
                                                Запретить
                                            </button>
                                        </form>
                                        <td>
                                            <form action="/admin_delete" method="post">
                                                <input type="hidden" name="post_text" value="<?= $status['text']; ?>">
                                                <button type="submit" name="delete" value="1" class="btn btn-danger"
                                                        onclick="return confirm('are you sure?')">Удалить
                                                </button>
                                            </form>
                                        </td>
                                    <?php } else { ?>
                                        <form action="/admin_allow" method="post">
                                            <input type="hidden" name="post_text" value="<?= $status['text']; ?>">
                                            <button type="submit" name="allow" class="btn btn-success" value="0">
                                                Разрешить
                                            </button>
                                        </form>
                                        <td>
                                            <form action="/admin_delete" method="post">
                                                <input type="hidden" name="post_text" value="<?= $status['text']; ?>">
                                                <button type="submit" name="delete" class="btn btn-danger"
                                                        onclick="return confirm('are you sure?')" value="1">Удалить
                                                </button>
                                            </form>
                                        </td>
                                        </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>