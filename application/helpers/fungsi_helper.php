<?php

function check_already_login()
{
	$ci = &get_instance();
	$user_session = $ci->session->userdata('id_user');
	if ($user_session) {
		redirect('dashboard');
	}
}

function check_not_login()
{
	$ci = &get_instance();
	$user_session = $ci->session->userdata('id_user');
	if (!$user_session) {
		redirect('auth/login');
	}
}

function check_status_lipa($nama_laporan, $bulan, $tahun)
{
	$ci = &get_instance();
	if ($nama_laporan == null) {
		$data = $ci->db->query("SELECT * FROM tbl_laporan WHERE bulan = $bulan AND tahun = $tahun");
	} else {
		$data = $ci->db->query("SELECT * FROM tbl_laporan WHERE nama_laporan = '$nama_laporan' AND bulan = $bulan AND tahun = $tahun");
	}
	return $data;
}

function indo_currency($nominal)
{
	$result = "Rp " . number_format($nominal, 2, ',', '.');
	return $result;
}

function rupiah($nominal)
{
	$result = "" . number_format($nominal, 2, ',', '.');
	return $result;
}


function indo_date($date)
{
	$d = substr($date, 8, 2);
	$m = substr($date, 5, 2);
	$y = substr($date, 0, 4);
	return $d . '/' . $m . '/' . $y;
}

function format_datetime_indonesia($datetime)
{
	return date('d-m-Y H:i:s', strtotime($datetime));
}

function hari_indo($hari)
{
	switch ($hari) {
		case 'Sun':
			$hari_ini = "Minggu";
			break;

		case 'Mon':
			$hari_ini = "Senin";
			break;

		case 'Tue':
			$hari_ini = "Selasa";
			break;

		case 'Wed':
			$hari_ini = "Rabu";
			break;

		case 'Thu':
			$hari_ini = "Kamis";
			break;

		case 'Fri':
			$hari_ini = "Jumat";
			break;

		case 'Sat':
			$hari_ini = "Sabtu";
			break;

		default:
			$hari_ini = "Tidak di ketahui";
			break;
	}

	return $hari_ini;
}

function tgl_indo($tanggal)
{
	$bulan = array(
		1 => 'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('-', $tanggal);

	// variabel pecahkan 0 = tanggal
	// variabel pecahkan 1 = bulan
	// variabel pecahkan 2 = tahun
	$hari = hari_indo(date('D', strtotime($tanggal)));

	return $pecahkan[2] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
}

function bulanIndo($bulan)
{
	if ($bulan == '01') {
		$bln = "JANUARI";
	} elseif ($bulan == '02') {
		$bln = "FEBRUARI";
	} elseif ($bulan == '03') {
		$bln = "MARET";
	} elseif ($bulan == '04') {
		$bln = "APRIL";
	} elseif ($bulan == '05') {
		$bln = "MEI";
	} elseif ($bulan == '06') {
		$bln = "JUNI";
	} elseif ($bulan == '07') {
		$bln = "JULI";
	} elseif ($bulan == '08') {
		$bln = "AGUSTUS";
	} elseif ($bulan == '09') {
		$bln = "SEPTEMBER";
	} elseif ($bulan == '10') {
		$bln = "OKTOBER";
	} elseif ($bulan == '11') {
		$bln = "NOVEMBER";
	} elseif ($bulan == '12') {
		$bln = "DESEMBER";
	} elseif ($bulan == '0') {
		$bln = "DESEMBER";
	}
	return $bln;
}
