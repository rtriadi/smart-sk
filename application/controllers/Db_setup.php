<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Db_setup extends CI_Controller
{
    public function index()
    {
        $this->load->dbforge();
        $this->load->database();

        echo "<h1>Smart SK Database Setup</h1>";

        // 1. Create tb_users
        if (!$this->db->table_exists('tb_users')) {
            $this->dbforge->add_field([
                'id_user' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'username' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'unique' => TRUE,
                ],
                'password' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'nama_pengguna' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE,
                ],
                'level' => [
                    'type' => 'ENUM("superadmin","admin")',
                    'default' => 'superadmin',
                ],
            ]);
            $this->dbforge->add_key('id_user', TRUE);
            $this->dbforge->create_table('tb_users');
            echo "<p style='color:green'>[SUCCESS] Table 'tb_users' created.</p>";
        } else {
            echo "<p style='color:orange'>[INFO] Table 'tb_users' already exists.</p>";
        }

        // 2. Insert Superadmin
        $admin = $this->db->get_where('tb_users', ['username' => 'admin'])->row();
        if (!$admin) {
            $data = [
                'username' => 'admin',
                // SHA1 hash for 'admin' (legacy CI3 auth style as seen in Auth.php)
                'password' => 'd033e22ae348aeb5660fc2140aec35850c4da997', 
                'nama_pengguna' => 'Super Administrator',
                'level' => 'superadmin'
            ];
            $this->db->insert('tb_users', $data);
            echo "<p style='color:green'>[SUCCESS] Superadmin user created (User: admin / Pass: admin).</p>";
        } else {
            echo "<p style='color:orange'>[INFO] Superadmin user already exists.</p>";
        }

        // 3. Add column 'created_by' to tb_sk_archives if missing
        if ($this->db->table_exists('tb_sk_archives')) {
            if (!$this->db->field_exists('created_by', 'tb_sk_archives')) {
                $fields = [
                    'created_by' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'default' => 0
                    ]
                ];
                $this->dbforge->add_column('tb_sk_archives', $fields);
                echo "<p style='color:green'>[SUCCESS] Column 'created_by' added to 'tb_sk_archives'.</p>";
            } else {
                echo "<p style='color:orange'>[INFO] Column 'created_by' already exists in 'tb_sk_archives'.</p>";
            }
        }

        echo "<hr><a href='".site_url('auth/login')."'>Go to Login Page</a>";
    }
}
