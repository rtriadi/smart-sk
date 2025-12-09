<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();
$CI->load->dbforge();

if (!$CI->db->table_exists('tb_pejabat')) {
    $fields = array(
        'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'nama' => array(
            'type' => 'VARCHAR',
            'constraint' => '255',
        ),
        'nip' => array(
            'type' => 'VARCHAR',
            'constraint' => '50',
            'null' => TRUE,
        ),
        'jabatan' => array(
            'type' => 'VARCHAR',
            'constraint' => '100',
        ),
        'status' => array(
            'type' => 'ENUM("aktif","non-aktif")',
            'default' => 'aktif',
        ),
    );
    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE);
    if($CI->dbforge->create_table('tb_pejabat')) {
        echo "Table tb_pejabat created successfully.\n";
        
        // Seed some initial data
        $data = [
            ['nama' => 'Drs. H. MURSIDIN, M.H.', 'nip' => '19650101 199003 1 001', 'jabatan' => 'Ketua'],
            ['nama' => 'Ahmad Syafiq, S.Ag.', 'nip' => '19700202 199503 1 002', 'jabatan' => 'Panitera']
        ];
        $CI->db->insert_batch('tb_pejabat', $data);
        echo "Seeded initial data.\n";
    } else {
        echo "Failed to create table.\n";
    }
} else {
    echo "Table tb_pejabat already exists.\n";
}
