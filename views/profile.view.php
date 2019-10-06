<?php $this -> layout( 'layout', [ 'title' => 'Страница профиля' ] ); ?>
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
                        <div class="card-header"><h3>Профиль пользователя</h3></div>

                        <div class="card-body">
                            <?php if (isset( $_SESSION['message_ok'] )) { ?>
                                <div class="alert alert-success" role="alert">
                                    <?= $_SESSION['message_ok']; ?>
                                    <?php unset( $_SESSION['message_ok'] ); ?>
                                </div>
                            <?php } ?>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name_control">Name</label>
                                            <input type="text" class="form-control" name="name"
                                                   id="name_control" value="<?= $_SESSION['auth_username']; ?>"
                                                   readonly>
                                        </div>
                                        <div class="form-group">
                                            <?php if (isset( $_SESSION['newEmail'] )) { ?>
                                                <label for="email_control">Email</label>
                                                <input type="email" class="form-control" name="newEmail"
                                                       id="email_control"
                                                       value="<?= $_SESSION['auth_email']; ?>">
                                                <span class="text text-danger">
                                                    <?= flash() -> display(); ?>
                                                </span>
                                            <? } else { ?>
                                                <label for="email_control">Email</label>
                                                <input type="email" class="form-control" name="newEmail"
                                                       id="email_control"
                                                       value="<?= $_SESSION['auth_email']; ?>">
                                                <span class="text text-danger">
                                                    <?= flash() -> display(); ?>
                                                </span>
                                            <?php } ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="avatar_control">Аватар</label>
                                            <input type="file" class="form-control" name="image"
                                                   id="avatar_control">
                                        </div>
                                    </div>
                                    <?php if (isset( $_SESSION['avatar_image'] )) { ?>
                                        <div class="col-md-4">
                                            <img src="<?= $_SESSION['avatar_image'] ?>" alt="" class="img-fluid">
                                        </div>
                                    <?php } elseif (!isset( $_SESSION['avatar_image'] )) { ?>
                                        <div class="col-md-4">
                                            <img src="../public/img/no-user.jpg" alt="" class="img-fluid">
                                        </div>
                                    <?php } ?>
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group ml-3 mr-2" role="group" aria-label="Save group button">
                                            <button type="submit" class="btn btn-warning">Save profile</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="card">
                        <div class="card-header"><h3>Безопасность</h3></div>

                        <div class="card-body">
                            <?php if (isset( $_SESSION['message_done'] )) { ?>
                                <div class="alert alert-success" role="alert">
                                    <?= $_SESSION['message_done']; ?>
                                    <?php unset( $_SESSION['message_done'] ); ?>
                                </div>
                            <?php } ?>
                            <form action="/profile_change_password" method="post">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="current_password">Current password</label>
                                            <?php if (isset( $_SESSION['login_password_change'] )) { ?>
                                                <input type="password" name="current" class="form-control is-invalid"
                                                       id="current_password">
                                                <span class="text text-danger">
                                                    <?= $_SESSION['login_password_change']; ?>
                                                </span>
                                                <?php unset( $_SESSION['login_password_change'] ); ?>
                                            <?php } else { ?>
                                                <input type="password" name="current" class="form-control"
                                                       id="current_password">
                                            <? } ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_password">New password</label>

                                            <?php if (isset( $_SESSION['password_change'] )) { ?>
                                                <input type="password" name="password" class="form-control is-invalid"
                                                       id="new_password" autofocus>
                                                <span class="text text-danger">
                                                    <?= $_SESSION['password_change']; ?>
                                                </span>
                                                <?php unset( $_SESSION['password_change'] ); ?>
                                            <? } else { ?>
                                                <input type="password" name="password" class="form-control"
                                                       id="new_password">
                                            <? } ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="password_confirmation">Password confirmation</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                   id="password_confirmation">
                                        </div>
                                        <button class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

