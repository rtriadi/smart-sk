<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update_template extends CI_Controller {
    public function index() {
        $this->load->database();
        
        // 1. Define New Form Config
        $form_config = [
            [
                "title" => "HEADER SK",
                "fields" => [
                    ["type" => "text", "label" => "Nomor Surat", "variable" => "no_sk", "default" => "W26-A/SK.OT1.6/XI/2025"],
                    ["type" => "textarea", "label" => "Tentang", "variable" => "judul_sk", "default" => "PEMBENTUKAN TIM KERJA REFORMASI BIROKRASI PENGADILAN AGAMA GORONTALO TAHUN 2025"],
                    ["type" => "date", "label" => "Tanggal Ditetapkan", "variable" => "tanggal_sk", "default" => "2025-12-01"]
                ]
            ],
            [
                "title" => "KONSIDERANS",
                "fields" => [
                    ["type" => "repeater", "label" => "Menimbang (Poin-poin)", "variable" => "list_menimbang"],
                    ["type" => "repeater", "label" => "Mengingat (Poin-poin)", "variable" => "list_mengingat"]
                ]
            ],
            [
                "title" => "PENANDATANGAN",
                "fields" => [
                    [
                        "type" => "select", 
                        "label" => "Pilih Jabatan", 
                        "variable" => "jabatan_selector", 
                        "options" => ["Ketua", "Wakil Ketua", "Panitera", "Sekretaris"],
                        "default" => "Ketua"
                    ],
                    ["type" => "text", "label" => "Jabatan (Otomatis)", "variable" => "jabatan_penandatangan", "default" => "KETUA PENGADILAN AGAMA GORONTALO"],
                    ["type" => "text", "label" => "Nama Pejabat", "variable" => "nama_penandatangan", "default" => "Drs. H. MURSIDIN, M.H."],
                    ["type" => "text", "label" => "NIP Pejabat", "variable" => "nip_penandatangan", "default" => "196707041994031003"],
                    ["type" => "checkbox", "label" => "Tampilkan Tanggal Hijriah", "variable" => "tampilkan_hijriah", "default" => true]
                ]
            ],
            [
                "title" => "SALINAN (Opsional)",
                "fields" => [
                    ["type" => "checkbox", "label" => "Tampilkan Salinan", "variable" => "tampilkan_salinan", "default" => false],
                    ["type" => "repeater", "label" => "Daftar Salinan", "variable" => "list_salinan"]
                ]
            ]
        ];

        // 2. Define New HTML Pattern
        $html_pattern = '
    <!-- KOP SURAT -->
    {{#if showKop}}
    <div class="header-kop text-center border-b-4 border-double border-black pb-2 mb-4">
        <table class="w-full">
            <tr>
                <td class="w-24 align-middle">
                    <img src="{{globalSettings.kopLogo}}" alt="Logo" class="w-20 mx-auto">
                </td>
                <td class="align-middle text-center">
                    <h3 class="font-bold text-lg uppercase">{{globalSettings.kopTitle1}}</h3>
                    <h2 class="font-bold text-lg uppercase">{{globalSettings.kopTitle2}}</h2>
                    <h2 class="font-bold text-lg uppercase">{{globalSettings.kopTitle3}}</h2>
                    <h1 class="font-bold text-lg uppercase">{{globalSettings.kopTitle4}}</h1>
                    <p class="text-xs italic">{{globalSettings.kopAddress}}</p>
                </td>
            </tr>
        </table>
    </div>
    {{/if}}

    <div class="text-center mb-6">
        <h4 class="font-bold uppercase">KEPUTUSAN {{jabatan_penandatangan}}</h4>
        <p class="font-bold uppercase">NOMOR: {{no_sk}}</p>
        <br>
        <p class="font-bold uppercase">TENTANG</p>
        <p class="font-bold uppercase">{{judul_sk}}</p>
        <br>
        <p class="font-bold uppercase">{{jabatan_penandatangan}},</p>
    </div>

    <div class="text-justify mb-6">
        <table class="w-full">
            <tr>
                <td class="w-32 font-bold align-top">Menimbang</td>
                <td class="w-4 align-top">:</td>
                <td class="align-top">
                    <ol class="list-lower-alpha pl-4 space-y-1">
                        {{#each list_menimbang}}
                        <li>{{this}}</li>
                        {{/each}}
                    </ol>
                </td>
            </tr>
            <tr>
                <td class="font-bold align-top">Mengingat</td>
                <td class="align-top">:</td>
                <td class="align-top">
                    <ol class="list-decimal pl-4 space-y-1">
                        {{#each list_mengingat}}
                        <li>{{this}}</li>
                        {{/each}}
                    </ol>
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center mb-6">
        <p class="font-bold uppercase">MEMUTUSKAN:</p>
    </div>

    <div class="text-justify mb-8">
        <table class="w-full space-y-2">
            <tr>
                <td class="w-32 font-bold align-top">Menetapkan</td>
                <td class="w-4 align-top">:</td>
                <td class="align-top font-bold uppercase">KEPUTUSAN {{jabatan_penandatangan}} TENTANG {{judul_sk}}.</td>
            </tr>
            <tr>
                <td class="font-bold align-top">KESATU</td>
                <td class="align-top">:</td>
                <td class="align-top">Membentuk Tim Kerja sebagaimana tercantum dalam lampiran keputusan ini.</td>
            </tr>
            <tr>
                <td class="font-bold align-top">KEDUA</td>
                <td class="align-top">:</td>
                <td class="align-top">Segala biaya yang timbul akibat keputusan ini dibebankan pada DIPA Pengadilan Agama Gorontalo.</td>
            </tr>
            <tr>
                <td class="font-bold align-top">KETIGA</td>
                <td class="align-top">:</td>
                <td class="align-top">Keputusan ini berlaku sejak tanggal ditetapkan.</td>
            </tr>
        </table>
    </div>

    <div class="mb-8">
        <table class="w-full">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    <p>Ditetapkan di Gorontalo</p>
                    <p>Pada tanggal {{tanggal_indo}}</p>
                    {{#if tampilkan_hijriah}}
                    <p>{{tanggal_hijri}}</p>
                    {{/if}}
                    <br>
                    <p class="font-bold uppercase">{{jabatan_selector}},</p>
                    <br><br><br>
                    <p class="font-bold underline uppercase">{{nama_penandatangan}}</p>
                    {{#if nip_penandatangan}}
                    <p>NIP. {{nip_penandatangan}}</p>
                    {{/if}}
                </td>
            </tr>
        </table>
    </div>

    {{#if tampilkan_salinan}}
    <div class="text-justify">
        <p>SALINAN Keputusan ini disampaikan kepada:</p>
        <ol class="list-decimal pl-4 space-y-1">
            {{#each list_salinan}}
            <li>{{this}}</li>
            {{/each}}
        </ol>
    </div>
    {{/if}}
';

        $data = [
            'form_config' => json_encode($form_config),
            'html_pattern' => $html_pattern
        ];
        
        $this->db->where('id', 1);
        $this->db->update('tb_templates', $data);
        
        echo "Template updated successfully.";
    }
}
