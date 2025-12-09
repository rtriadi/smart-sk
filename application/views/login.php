<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>e-Laporan | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/custom.css">
</head>

<body class="hold-transition login-page" style="background-image: url('../assets/dist/img/login.png'); background-size: 100%    ; background-position: center;">

    <div class="login-box">
        <div class="login-logo">
            <!--     -->
            <!-- <a href="#"><b>e-</b>Laporan</a>  -->
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <?php
                if ($this->session->flashdata('error')) { ?>
                    <div class="alert alert-danger alert-dismissible " role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span>
                        </button>
                        <strong>Gagal </strong>
                        <?= $this->session->flashdata('error') ?>
                    </div>
                <?php }
                ?>
                <!-- <p class="login-box-msg">Sign in to start your session</p> -->

                <form action="<?= site_url('auth/login') ?>" method="post">
                    <div class="input-group mb-3">
                        <input name="username" type="text" class="form-control" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input name="password" type="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">

                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-arrow-right"></i>
                                Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>        
    </div>

    
    <!-- jQuery -->
    <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>


    <footer class="footer" style="width: 100%; background-color: #f8f9fa; position: absolute; bottom: 0; left: 0; padding: 8px;">
    <div class="float-right d-none d-md-block">
        <!-- <b>Version</b> 1.0.0 -->
    </div>
    <center><strong>Copyright &copy; 2024 <a href="https://www.pta-gorontalo.go.id">Pengadilan Tinggi Agama Gorontalo</a>.</strong> All rights reserved.
</footer>


   
</body>

<br>

                
</html>