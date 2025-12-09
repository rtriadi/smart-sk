<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-header">MAIN MENU</li>
    <li class="nav-item">
        <a href="<?= site_url('/dashboard') ?>" class="nav-link <?= $page == 'Dashboard' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-home"></i>
            <p>
                Dashboard
                <!-- <span class="right badge badge-danger">New</span> -->
            </p>
        </a>
    </li>
    <li
        class="nav-item <?= $page == 'E-Court' || $page == 'Kinerja' || $page == 'Detail Kinerja' ? 'menu-open' : '' ?>">
        <a href="#"
            class="nav-link <?= $page == 'E-Court' || $page == 'Kinerja' || $page == 'Detail Kinerja' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-balance-scale"></i>
            <p>
                Monitoring
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="<?= site_url('/ecourt') ?>" class="nav-link <?= $page == 'E-Court' ? 'active' : '' ?>">
                    <i class="nav-icon far fa-circle"></i>
                    <p>
                        E-Court
                        <!-- <span class="right badge badge-danger">New</span> -->
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/kinerja') ?>"
                    class="nav-link <?= $page == 'Kinerja' || $page == 'Detail Kinerja' ? 'active' : '' ?>">
                    <i class="nav-icon far fa-circle"></i>
                    <p>
                        Kinerja (IKU)
                        <!-- <span class="right badge badge-danger">New</span> -->
                    </p>
                </a>
            </li>
        </ul>
    </li>
    <li class="nav-header">Laporan</li>
    <li class="nav-item 
    <?php
    for ($i = 1; $i < 24; $i++) {
        if ($page == 'Lipa ' . $i) {
            echo 'menu-open';
        }
    }
    ?>
    ">
        <a href="#" class="nav-link 
        <?php
        for ($i = 1; $i < 23; $i++) {
            if ($page == 'Lipa ' . $i) {
                echo 'active';
            }
        }
        ?>">
            <i class="nav-icon far fa-file"></i>
            <p>
                LIPA
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="<?= site_url('/lipa1') ?>" class="nav-link <?= $page == 'Lipa 1' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 1 (Keadaan Perkara)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa2') ?>" class="nav-link <?= $page == 'Lipa 2' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 2 (Permohonan Banding)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa3') ?>" class="nav-link <?= $page == 'Lipa 3' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 3 (Permohonan Kasasi)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa4') ?>" class="nav-link <?= $page == 'Lipa 4' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 4(Permohonan PK)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa5') ?>" class="nav-link <?= $page == 'Lipa 5' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 5 (Permohonan Eksekusi)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa6') ?>" class="nav-link <?= $page == 'Lipa 6' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 6 (Kegiatan Hakim)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa7') ?>" class="nav-link <?= $page == 'Lipa 7' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 7 (Keuangan Perkara)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa72') ?>" class="nav-link <?= $page == 'Lipa 72' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 7b <br>(Keuangan Perkara Eksekusi)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa75') ?>" class="nav-link <?= $page == 'Lipa 75' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 7c <br>(Keuangan Perkara Konsinyasi)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa8') ?>" class="nav-link <?= $page == 'Lipa 8' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 8 <br>(Perkara Diterima, Cabut, Putus)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa9') ?>" class="nav-link <?= $page == 'Lipa 9' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 9 <br>(Perkara Khusus PP. N0.10 Tahun 1983 JO. PP. No.45 Tahun 1990)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa10') ?>" class="nav-link <?= $page == 'Lipa 10' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 10 <br>(Faktor Penyebab Perceraian)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa11') ?>" class="nav-link <?= $page == 'Lipa 11' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 11 <br>(Pertanggungjawaban Iwadh)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa12') ?>" class="nav-link <?= $page == 'Lipa 12' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 12 (Mediasi)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa121') ?>" class="nav-link <?= $page == 'Lipa 121' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 12a (Penyelesaian Mediasi)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa13') ?>" class="nav-link <?= $page == 'Lipa 13' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 13 (Penerbitan Akta Cerai)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa14') ?>" class="nav-link <?= $page == 'Lipa 14' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 14 <br>(Pelaksanaan Sidang di Luar Gedung)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa15') ?>" class="nav-link <?= $page == 'Lipa 15' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 15 <br>(Pelaksanaan Pembebasan Biaya Perkara)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa16') ?>" class="nav-link <?= $page == 'Lipa 16' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 16 <br>(Pelaksanaan Posbakum)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa17') ?>" class="nav-link <?= $page == 'Lipa 17' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 17 <br>(Penerimaan Hak-Hak Kepaniteraan - HHK)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa18') ?>" class="nav-link <?= $page == 'Lipa 18' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 18<br>(Hak-Hak Kepaniteraan lainnya - HHKL)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa19') ?>" class="nav-link <?= $page == 'Lipa 19' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 19 (Minutasi Perkara)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa20') ?>" class="nav-link <?= $page == 'Lipa 20' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 20 <br>(Tingkat Penyelesaian Perkara)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa21') ?>" class="nav-link <?= $page == 'Lipa 21' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 21 <br>(Verzet Terhadap Putusan Verstek)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa22') ?>" class="nav-link <?= $page == 'Lipa 22' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 22 <br>(Penanganan Bantuan Panggilan/Pemberitahuan)</p>
                    </small>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('/lipa23') ?>" class="nav-link <?= $page == 'Lipa 23' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <small>
                        <p>LIPA 23 (Sidang Terpadu)</p>
                    </small>
                </a>
            </li>
        </ul>
    </li>
    <?php if ($this->fungsi->user_login()->role == 'Admin'): ?>
        <li class="nav-header">Manual</li>
        <li class="nav-item">
            <a href="<?= site_url('skm') ?>" class="nav-link <?= $page == 'SKM' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>
                    SKM
                    <!-- <span class="right badge badge-danger">New</span> -->
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('sidkel') ?>" class="nav-link <?= $page == 'Sidang Keliling' ? 'active' : '' ?>">
                <i class="nav-icon fa fa-university"></i>
                <p>
                    Sidang Keliling
                    <!-- <span class="right badge badge-danger">New</span> -->
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('posbakum') ?>" class="nav-link <?= $page == 'Posbakum' ? 'active' : '' ?>">
                <i class="nav-icon fa fa-child"></i>
                <p>
                    Posbakum
                    <!-- <span class="right badge badge-danger">New</span> -->
                </p>
            </a>
        </li>
        <li class="nav-item <?= $page == 'Pengawasan' || $page == 'Zona Integritas' || $page == 'SKP' || $page == 'IKPA' || $page == 'AKIP' || $page == 'BMN' ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= $page == '' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-th-large"></i>
                <p>
                    Indikator Kinerja
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= site_url('/pengawasan') ?>" class="nav-link <?= $page == 'Pengawasan' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            (IK.1.1) Pengawasan
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('/zi') ?>" class="nav-link <?= $page == 'Zona Integritas' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            (IK.1.2) Zona Integritas
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('/skp') ?>" class="nav-link <?= $page == 'SKP' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            (IK.2.1) SKP
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('/ikpa') ?>" class="nav-link <?= $page == 'IKPA' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            (IK.2.2) IKPA
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('/akip') ?>" class="nav-link <?= $page == 'AKIP' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            (IK.2.3) AKIP
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('/bmn') ?>" class="nav-link <?= $page == 'BMN' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            (IK.2.4) BMN
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-header">Konfigurasi</li>
        <!--  <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>
                    User
                    <span class="right badge badge-danger">New</span>
                </p>
            </a>
        </li> -->
        <li class="nav-item">
            <a href="<?= site_url('target_prodeo') ?>" class="nav-link <?= $page == 'Target Prodeo' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-bullseye"></i>
                <p>
                    Target Prodeo
                    <!-- <span class="right badge badge-danger">New</span> -->
                </p>
            </a>
        </li>
        <li class="nav-item <?= $page == 'Sasaran Strategis' || $page == 'Indikator Kinerja' ? 'menu-open' : '' ?>">
            <a href="#"
                class="nav-link <?= $page == 'Sasaran Strategis' || $page == 'Indikator Kinerja' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-edit"></i>
                <p>
                    Master Kinerja
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= site_url('/sasaran_strategis') ?>"
                        class="nav-link <?= $page == 'Sasaran Strategis' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            Sasaran Strategis
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('/indikator_kinerja') ?>"
                        class="nav-link <?= $page == 'Indikator Kinerja' ? 'active' : '' ?>">
                        <i class="nav-icon far fa-circle"></i>
                        <p>
                            Indikator Kinerja
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
            </ul>
        </li>
    <?php endif ?>
</ul>