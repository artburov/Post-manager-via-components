<?php $this->layout('layout', ['title' => 'Страница логина']);?>
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
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/registration">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Login</div>

                        <div class="card-body">
                            <form method="POST" action="">

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail
                                        Address</label>
                                    <!--If registration is success-->
                                    <?php if (isset( $_SESSION['login_email_success'] )) { ?>
                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control is-valid " name="email" autofocus>
                                            <span class="valid-feedback">
                                                    <strong><?php echo $_SESSION['login_email_success']; ?></strong>
                                                </span>
                                            <?php unset( $_SESSION['login_email_success'] ); ?>
                                        </div>
                                    <?php } elseif (isset( $_SESSION['login_email'] )) { ?>
                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control is-invalid " name="email"
                                                   autocomplete="email" autofocus>
                                            <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo $_SESSION['login_email']; ?></strong>
                                                </span>
                                        </div>
                                        <? unset( $_SESSION['login_email'] ); ?>
                                    <? } else { ?>
                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control" name="email"
                                                   autocomplete="email" autofocus>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                    <?php if (isset( $_SESSION['login_password'] )) { ?>
                                        <div class="col-md-6">
                                            <input id="password" type="password" class="form-control is-invalid"
                                                   name="password" autocomplete="current-password">
                                            <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo $_SESSION['login_password']; ?></strong>
                                                </span>
                                        </div>
                                        <? unset( $_SESSION['login_password'] ); ?>
                                    <? } else { ?>
                                        <div class="col-md-6">
                                            <input id="password" type="password" class="form-control" name="password"
                                                   autocomplete="current-password">
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                   id="remember" value="1">

                                            <label class="form-check-label" for="remember">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Login
                                        </button>
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

