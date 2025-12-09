<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_smart_sk";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example Template: SK Tim Kerja
$nama_sk = "SK Tim Kerja Reformasi Birokrasi";
$kategori = "ortala";
$nomor_pattern = "W26-A/SK.OT1.6/{bulan}/{tahun}";

// 1. Form Config (JSON)
$form_config = [
    [
        "section" => "Header SK",
        "fields" => [
            ["label" => "Nomor Surat", "variable" => "no_sk", "type" => "text", "default" => "W26-A/SK.OT1.6/XI/2025"],
            ["label" => "Tentang", "variable" => "judul_sk", "type" => "textarea", "rows" => 2, "default" => "PEMBENTUKAN TIM KERJA REFORMASI BIROKRASI PENGADILAN AGAMA GORONTALO TAHUN 2025"],
            ["label" => "Tanggal Ditetapkan", "variable" => "tanggal_sk", "type" => "date", "default" => date('Y-m-d')]
        ]
    ],
    [
        "section" => "Konsiderans",
        "fields" => [
            [
                "label" => "Menimbang (Poin-poin)", 
                "variable" => "list_menimbang", 
                "type" => "repeater"
            ],
            [
                "label" => "Mengingat (Poin-poin)", 
                "variable" => "list_mengingat", 
                "type" => "repeater"
            ]
        ]
    ],
    [
        "section" => "Penandatangan",
        "fields" => [
            ["label" => "Nama Ketua", "variable" => "nama_penandatangan", "type" => "text", "default" => "Drs. H. MURSIDIN, M.H."],
            ["label" => "NIP Ketua", "variable" => "nip_penandatangan", "type" => "text", "default" => "19650101 199003 1 001"]
        ]
    ]
];

// 2. HTML Pattern
$html_pattern = '
<div class="header-kop" style="text-align: center; border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 20px;">
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/Logo_Mahkamah_Agung_RI.png/1200px-Logo_Mahkamah_Agung_RI.png" style="height: 80px;">
    <h3 style="margin: 5px 0;">PENGADILAN AGAMA GORONTALO KELAS IA</h3>
    <p style="margin: 0; font-size: 10pt;">Jl. Drs. Achmad Nadjamuddin No. 1, Kota Gorontalo</p>
</div>

<div class="judul-sk" style="text-align: center; margin-bottom: 20px;">
    <h4 style="margin: 0; text-decoration: underline;">KEPUTUSAN KETUA PENGADILAN AGAMA GORONTALO</h4>
    <p style="margin: 5px 0;">NOMOR: {{no_sk}}</p>
    <br>
    <p style="margin: 0;">TENTANG</p>
    <p style="margin: 5px 0; font-weight: bold; text-transform: uppercase;">{{judul_sk}}</p>
    <br>
    <p style="margin: 0;">KETUA PENGADILAN AGAMA GORONTALO,</p>
</div>

<div class="konsiderans">
    <table style="width: 100%; vertical-align: top;">
        <tr>
            <td width="100" valign="top">Menimbang</td>
            <td width="10" valign="top">:</td>
            <td>
                <ol type="a" style="margin: 0; padding-left: 20px;">
                    {{#each list_menimbang}}
                    <li>{{this}}</li>
                    {{/each}}
                </ol>
            </td>
        </tr>
        <tr>
            <td valign="top">Mengingat</td>
            <td valign="top">:</td>
            <td>
                <ol type="1" style="margin: 0; padding-left: 20px;">
                    {{#each list_mengingat}}
                    <li>{{this}}</li>
                    {{/each}}
                </ol>
            </td>
        </tr>
    </table>
</div>

<div class="diktum" style="margin-top: 20px;">
    <p style="text-align: center; font-weight: bold;">MEMUTUSKAN:</p>
    <table style="width: 100%; vertical-align: top;">
        <tr>
            <td width="100" valign="top">Menetapkan</td>
            <td width="10" valign="top">:</td>
            <td><b>KEPUTUSAN KETUA PENGADILAN AGAMA GORONTALO TENTANG {{judul_sk}}.</b></td>
        </tr>
        <tr>
            <td valign="top">KESATU</td>
            <td valign="top">:</td>
            <td>Membentuk Tim Kerja sebagaimana tercantum dalam lampiran keputusan ini.</td>
        </tr>
        <tr>
            <td valign="top">KEDUA</td>
            <td valign="top">:</td>
            <td>Segala biaya yang timbul akibat keputusan ini dibebankan pada DIPA Pengadilan Agama Gorontalo.</td>
        </tr>
        <tr>
            <td valign="top">KETIGA</td>
            <td valign="top">:</td>
            <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
        </tr>
    </table>
</div>

<div class="ttd-area" style="margin-top: 50px; float: right; width: 40%;">
    <p>Ditetapkan di Gorontalo</p>
    <p>Pada tanggal {{tanggal_sk}}</p>
    <br>
    <p>KETUA,</p>
    <br><br><br>
    <p style="font-weight: bold; text-decoration: underline;">{{nama_penandatangan}}</p>
    <p>NIP. {{nip_penandatangan}}</p>
</div>
';

$stmt = $conn->prepare("INSERT INTO tb_templates (nama_sk, kategori, nomor_pattern, html_pattern, form_config) VALUES (?, ?, ?, ?, ?)");
$json_config = json_encode($form_config);
$stmt->bind_param("sssss", $nama_sk, $kategori, $nomor_pattern, $html_pattern, $json_config);

if ($stmt->execute()) {
    echo "Template '$nama_sk' seeded successfully.\n";
} else {
    echo "Error seeding template: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
