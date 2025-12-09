<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Db_check extends CI_Controller
{
    public function index()
    {
        $this->load->database();
        if ($this->db->table_exists('tb_pejabat')) {
            echo "Table 'tb_pejabat' exists.<br>";
            $fields = $this->db->list_fields('tb_pejabat');
            echo "Columns: " . implode(', ', $fields);
            
            // Show data
            $query = $this->db->get('tb_pejabat');
            echo "<pre>";
            print_r($query->result_array());
            echo "</pre>";
        } else {
            echo "Table 'tb_pejabat' DOES NOT exist. Creating it now...<br>";
             $this->load->dbforge();
             $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'nama' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'nip' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'jabatan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'status' => [
                    'type' => 'ENUM("aktif","nonaktif")',
                    'default' => 'aktif',
                ],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('tb_pejabat');
            echo "Table 'tb_pejabat' created successfully.<br>";
            
            // Insert dummy data
            $data = [
                [
                    'nama' => 'Drs. H. MURSIDIN, M.H.',
                    'nip' => '196607071992031003',
                    'jabatan' => 'KETUA PENGADILAN AGAMA GORONTALO',
                    'status' => 'aktif'
                ],
                [
                    'nama' => 'Drs. H. MURSIDIN, M.H. (Wakil)',
                    'nip' => '196607071992031003',
                    'jabatan' => 'WAKIL KETUA PENGADILAN AGAMA GORONTALO',
                    'status' => 'aktif'
                ]
            ];
            $this->db->insert_batch('tb_pejabat', $data);
            echo "Dummy data inserted.";
        }
    }
}
