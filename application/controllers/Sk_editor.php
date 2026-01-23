<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sk_editor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Custom autoloader for Dompdf and dependencies (avoids PHPUnit compatibility issues)
        spl_autoload_register(function ($class) {
            $namespaces = [
                'Dompdf\\' => FCPATH . 'vendor/dompdf/dompdf/src/',
                'FontLib\\' => FCPATH . 'vendor/phenx/php-font-lib/src/FontLib/',
                'Sabr\\' => FCPATH . 'vendor/phenx/php-font-lib/src/Sabr/',
                'Svg\\' => FCPATH . 'vendor/phenx/php-svg-lib/src/Svg/',
                'Masterminds\\' => FCPATH . 'vendor/masterminds/html5/src/',
            ];
            
            foreach ($namespaces as $prefix => $baseDir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) === 0) {
                    $relativeClass = substr($class, $len);
                    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
                    if (file_exists($file)) {
                        require_once $file;
                        return true;
                    }
                }
            }
            
            // Handle Cpdf legacy class
            if ($class === 'Dompdf\Cpdf') {
                $file = FCPATH . 'vendor/dompdf/dompdf/lib/Cpdf.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            return false;
        });

        check_not_login(); // Enforce Auth
        $this->load->model('Template_model');
        $this->load->model('Archive_model');
        $this->load->model('Pejabat_model');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
    }

    public function api_get_templates() {
        $templates = $this->Template_model->get_all_templates();
        $result = [];
        foreach ($templates as $t) {
            $result[] = [
                'id' => $t->id,
                'name' => $t->nama_sk,
                'kategori' => $t->kategori
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function index() {
        $data['archives'] = $this->Archive_model->get_all_archives();
        $data['templates'] = $this->Template_model->get_all_templates();
        
        // Wrap in enterprise layout
        $layout_data['page_content'] = $this->load->view('sk_editor/dashboard', $data, TRUE);
        $this->load->view('layout/enterprise_layout', $layout_data);
    }

    public function settings() {
        $this->load->model('Category_model');
        $data['categories'] = $this->Category_model->get_all();
        $data['pejabat'] = $this->Pejabat_model->get_all();
        
        // Wrap in enterprise layout
        $layout_data['page_content'] = $this->load->view('sk_editor/settings_view', $data, TRUE);
        $this->load->view('layout/enterprise_layout', $layout_data);
    }

    public function save_pejabat() {
        $data = $this->input->post();
        if (isset($data['id']) && $data['id']) {
            $this->Pejabat_model->update($data['id'], $data);
        } else {
            $this->Pejabat_model->insert($data);
        }
        redirect('sk_editor/settings');
    }

    public function delete_pejabat($id) {
        $this->Pejabat_model->delete($id);
        redirect('sk_editor/settings');
    }

    public function set_default_pejabat($id) {
        $this->Pejabat_model->set_default($id);
        redirect('sk_editor/settings');
    }
    
    // Category CRUD
    public function save_category() {
        $data = $this->input->post();
        $this->load->model('Category_model');
        if (isset($data['id']) && $data['id']) {
            $this->Category_model->update($data['id'], $data);
        } else {
            $this->Category_model->insert($data);
        }
        redirect('sk_editor/settings');
    }

    public function delete_category($id) {
        $this->load->model('Category_model');
        $this->Category_model->delete($id);
        redirect('sk_editor/settings');
    }

    public function archives() {
        $data['archives'] = $this->Archive_model->get_all_archives();
        
        // Wrap in enterprise layout
        $layout_data['page_content'] = $this->load->view('sk_editor/archive_view', $data, TRUE);
        $this->load->view('layout/enterprise_layout', $layout_data);
    }

    public function create($template_id = null) {
        if (!$template_id) {
            redirect('templates'); // Redirect to template manager to pick one
            return;
        }
        $template = $this->Template_model->get_template_by_id($template_id);
        if (!$template) {
            redirect('sk_editor');
            return;
        }
        $data['template'] = $template;
        $data['draft_data'] = null;
        $data['archive_id'] = null;
        $data['pejabat'] = $this->Pejabat_model->get_active(); // Pass active pejabat
        $this->load->view('sk_editor/editor_view', $data);
    }

    public function edit_draft($archive_id) {
        $archive = $this->Archive_model->get_archive_by_id($archive_id);
        if (!$archive) show_404();

        $data['template'] = $this->Template_model->get_template_by_id($archive->template_id);
        $data['draft_data'] = $archive->input_data_json;
        $data['draft_settings'] = $archive->settings_json; // Pass settings
        $data['archive_id'] = $archive->id;
        $data['pejabat'] = $this->Pejabat_model->get_active(); // Pass active pejabat
        
        $this->load->view('sk_editor/editor_view', $data);
    }

    public function save() {
        // AJAX handler to save JSON data
        $input_data = $this->input->post('data');
        $settings_json = $this->input->post('settings'); // Get settings
        $template_id = $this->input->post('template_id');
        $archive_id = $this->input->post('archive_id');
        
        // Check for oversized payload or empty post
        if (empty($input_data) && empty($settings_json)) {
             echo json_encode(['status' => 'error', 'message' => 'No data received. Payload might be too large (max post size exceeded).']);
             return;
        }
        
        // Check for excessively large payload (likely uncompressed image) to prevent DB crash
        // Check for excessively large payload (likely uncompressed image) to prevent DB crash (max_allowed_packet is 1MB)
        if (strlen($input_data) > 950000) { // Limit to ~950KB to be safe below 1MB default
             $sizeMB = round(strlen($input_data) / 1048576, 2);
             echo json_encode(['status' => 'error', 'message' => "Data too large ({$sizeMB}MB). Server limit is 1MB. Please use a smaller logo or ask admin to increase 'max_allowed_packet'."]);
             return;
        }

        if ($archive_id && $archive_id !== 'null') {
            // Update existing
            $update_data = [
                'input_data_json' => $input_data,
                'settings_json' => $settings_json,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $archive_id);
            if ($this->db->update('tb_sk_archives', $update_data)) {
                echo json_encode(['status' => 'success', 'id' => $archive_id]);
            } else {
                $error = $this->db->error();
                echo json_encode(['status' => 'error', 'message' => $error['message']]);
            }
        } else {
            // Create new
            $no_surat = 'DRAFT-' . date('YmdHis');
            $user_id = $this->session->userdata('id_user') ? $this->session->userdata('id_user') : 0;
            
            $save_data = [
                'template_id' => $template_id,
                'input_data_json' => $input_data,
                'settings_json' => $settings_json,
                'created_by' => $user_id,
                'no_surat' => $no_surat
            ];

            if ($this->Archive_model->create_archive($save_data)) {
                 $new_id = $this->db->insert_id();
                echo json_encode(['status' => 'success', 'id' => $new_id]);
            } else {
                $error = $this->db->error();
                echo json_encode(['status' => 'error', 'message' => $error['message']]);
            }
        }
    }

    public function clone_draft($archive_id) {
        $archive = $this->Archive_model->get_archive_by_id($archive_id);
        if (!$archive) show_404();

        // Prepare new data
        $new_data = [
            'template_id' => $archive->template_id,
            'input_data_json' => $archive->input_data_json,
            'settings_json' => $archive->settings_json,
            'created_by' => $archive->created_by,
            'no_surat' => $archive->no_surat . ' (Copy)',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->Archive_model->create_archive($new_data)) {
            // Redirect to archives with success message if possible, or just redirect
            redirect('sk_editor/archives');
        } else {
            show_error('Failed to clone draft.');
        }
    }

    public function rename_draft() {
        // AJAX Handler
        $id = $this->input->post('id');
        $new_name = $this->input->post('name');
        
        if (!$id || !$new_name) {
             echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
             return;
        }

        $this->Archive_model->update_archive($id, ['no_surat' => $new_name]);
        echo json_encode(['status' => 'success']);
    }

    public function finalize_draft() {
        // AJAX Handler - Finalize a draft to become official SK
        $id = $this->input->post('id');
        $no_surat = $this->input->post('no_surat');
        $status = $this->input->post('status');
        
        if (!$id || !$no_surat) {
             echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
             return;
        }

        $update_data = [
            'no_surat' => $no_surat,
            'status' => $status ?: 'final',
            'finalized_at' => date('Y-m-d H:i:s')
        ];
        
        $this->Archive_model->update_archive($id, $update_data);
        echo json_encode(['status' => 'success']);
    }

    public function update_status() {
        // AJAX Handler - Update status (draft/final)
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        
        if (!$id || !$status) {
             echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
             return;
        }

        $update_data = ['status' => $status];
        
        // If reverting to draft, clear finalized_at
        if ($status === 'draft') {
            $update_data['finalized_at'] = null;
        }
        
        $this->Archive_model->update_archive($id, $update_data);
        echo json_encode(['status' => 'success']);
    }

    public function generate_pdf($archive_id) {
        $html = $this->_prepare_sk_html($archive_id, 'pdf');
        
        $archive = $this->Archive_model->get_archive_by_id($archive_id); // Need archive for filename

        // Generate PDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        
        // Settings for paper size
        $settings = json_decode($archive->settings_json, true);
        $paperSize = isset($settings['paperSize']) ? $settings['paperSize'] : 'A4';
        $orientation = isset($settings['orientation']) ? $settings['orientation'] : 'portrait';
        
        if ($paperSize === 'F4') {
            $paperSize = [0, 0, 609.4488, 935.433];
        }
        
        $dompdf->setPaper($paperSize, $orientation);
        $dompdf->render();
        $dompdf->stream("SK_" . $archive->no_surat . ".pdf", array("Attachment" => 0));
    }

    public function print_draft($archive_id) {
        // We don't need to pass HTML here anymore, just the ID
        $data['archive_id'] = $archive_id;
        $this->load->view('sk_editor/print_view', $data);
    }

    public function preview_content($archive_id) {
        // This endpoint outputs the raw HTML for the iframe
        echo $this->_prepare_sk_html($archive_id, 'web');
    }

    public function delete_draft($archive_id) {
        $this->Archive_model->delete_archive($archive_id);
        redirect('sk_editor/archives');
    }

    // Debug endpoint - remove in production
    public function debug_archive($archive_id) {
        $archive = $this->Archive_model->get_archive_by_id($archive_id);
        $template = $this->Template_model->get_template_by_id($archive->template_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'archive_id' => $archive_id,
            'template_id' => $archive->template_id,
            'no_surat' => $archive->no_surat,
            'input_data' => json_decode($archive->input_data_json, true),
            'settings' => json_decode($archive->settings_json, true),
            'html_pattern_length' => strlen($template->html_pattern),
            'html_pattern_preview' => substr($template->html_pattern, 0, 500)
        ], JSON_PRETTY_PRINT);
    }

    private function _prepare_sk_html($archive_id, $mode = 'pdf') {
        $archive = $this->Archive_model->get_archive_by_id($archive_id);
        if (!$archive) show_404();

        $template = $this->Template_model->get_template_by_id($archive->template_id);
        $input_data = json_decode($archive->input_data_json, true);
        $settings = json_decode($archive->settings_json, true);
        $html = $template->html_pattern;

        // Merge Settings into Data
        $data = $input_data ?: [];
        $data['globalSettings'] = $settings;

        // 1. CONVERT HANDLEBARS SYNTAX TO MUSTACHE
        // {{#each var}}...{{/each}} -> {{#var}}...{{/var}}
        $html = preg_replace_callback('/{{#each\s+(\w+)}}(.*?){{\/each}}/s', function($matches) {
            $varName = $matches[1];
            $content = $matches[2];
            return '{{#' . $varName . '}}' . $content . '{{/' . $varName . '}}';
        }, $html);
        
        // {{#if var}}...{{/if}} -> {{#var}}...{{/var}}
        $html = preg_replace_callback('/{{#if\s+(\w+)}}(.*?){{\/if}}/s', function($matches) {
            $varName = $matches[1];
            $content = $matches[2];
            return '{{#' . $varName . '}}' . $content . '{{/' . $varName . '}}';
        }, $html);

        // 2. MUSTACHE RENDER
        // Load Mustache v3+ with namespace
        $mustacheSrc = FCPATH . 'vendor/mustache/mustache/src/';
        
        // Register Mustache namespace autoloader
        spl_autoload_register(function ($class) use ($mustacheSrc) {
            if (strpos($class, 'Mustache\\') === 0) {
                $relative = substr($class, 9);
                $file = $mustacheSrc . str_replace('\\', '/', $relative) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            return false;
        });

        try {
            $m = new \Mustache\Engine([
                'entity_flags' => ENT_QUOTES,
                'escape' => function($value) {
                    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            ]);

            // PRE-PROCESS DATA (Newlines -> <br>)
            if (is_array($data)) {
                array_walk_recursive($data, function(&$item) {
                    if (is_string($item)) {
                         $item = nl2br($item);
                    }
                });
            }

            $html = $m->render($html, $data ?: []);

        } catch (\Exception $e) {
            error_log('Smart-SK Mustache Error: ' . $e->getMessage());
            $html .= "<br><b>Render Error: " . $e->getMessage() . "</b>";
        } catch (\Error $e) {
            error_log('Smart-SK Mustache Fatal Error: ' . $e->getMessage());
            $html .= "<br><b>Fatal Error: " . $e->getMessage() . "</b>";
        }

        // 3. Convert pagebreak comments to visible elements
        $html = str_replace('<!-- pagebreak -->', '<div class="mce-pagebreak"></div>', $html);

        // 4. CSS Injection (Logic remains same, just after render)
        if ($settings) {
            $marginTop = isset($settings['marginTop']) ? $settings['marginTop'] . 'mm' : '20mm';
            $marginBottom = isset($settings['marginBottom']) ? $settings['marginBottom'] . 'mm' : '20mm';
            $marginLeft = isset($settings['marginLeft']) ? $settings['marginLeft'] . 'mm' : '25mm';
            $marginRight = isset($settings['marginRight']) ? $settings['marginRight'] . 'mm' : '20mm';

            // Base CSS
            $bookosPath = FCPATH . 'assets/BOOKOS.TTF';
            $bookosbPath = FCPATH . 'assets/BOOKOSB.TTF';
            
            $css = "<style>
            @font-face {
                font-family: 'Bookman Old Style';
                src: url('{$bookosPath}') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            @font-face {
                font-family: 'Bookman Old Style';
                src: url('{$bookosbPath}') format('truetype');
                font-weight: bold;
                font-style: normal;
            }

            body {
                font-family: 'Bookman Old Style', serif;
                font-size: 12pt;
                line-height: 1.5;
            }
            table { border-collapse: collapse; width: 100%; }
            td, th { padding: 0; vertical-align: top; }
            img { max-width: 100%; }
            
            /* Utilities */
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .text-justify { text-align: justify; }
            .font-bold { font-weight: bold; }
                .uppercase { text-transform: uppercase; }
                .underline { text-decoration: underline; }
                .italic { font-style: italic; }
                
                /* Margins */
                .mb-4 { margin-bottom: 1rem; }
                .mb-8 { margin-bottom: 2rem; }
            ";

            if ($mode === 'pdf') {
                $css .= "
                    @page {
                        margin-top: {$marginTop};
                        margin-bottom: {$marginBottom};
                        margin-left: {$marginLeft};
                        margin-right: {$marginRight};
                    }
                    body { margin: 0; padding: 0; }
                ";
            } else {
                // Web Preview Mode - CSS for the Iframe content
                $width = ($settings['paperSize'] === 'F4') ? '215mm' : '210mm'; 
                $minHeight = ($settings['paperSize'] === 'F4') ? '330mm' : '297mm';

                $css .= "
                    body {
                        background-color: #525659;
                        margin: 0;
                        padding: 2rem;
                        display: flex;
                        justify-content: center;
                    }
                    .page-container {
                        background-color: white;
                        width: {$width};
                        min-height: {$minHeight};
                        padding-top: {$marginTop};
                        padding-bottom: {$marginBottom};
                        padding-left: {$marginLeft};
                        padding-right: {$marginRight};
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                        box-sizing: border-box; 
                    }
                    /* Page break handling */
                    .mce-pagebreak {
                        display: block;
                        height: 2px;
                        border: 0;
                        border-top: 2px dashed #0d9488;
                        margin: 1em 0;
                        page-break-after: always;
                    }
                    @media print {
                        @page {
                            size: {$width} auto;
                            margin-top: {$marginTop};
                            margin-bottom: {$marginBottom};
                            margin-left: {$marginLeft};
                            margin-right: {$marginRight};
                        }
                        body {
                            background: white;
                            padding: 0;
                            display: block;
                        }
                        .page-container {
                            width: 100%;
                            min-height: auto;
                            box-shadow: none;
                            padding: 0;
                            margin: 0;
                        }
                        .mce-pagebreak {
                            display: block;
                            height: 0;
                            border: none;
                            margin: 0;
                            page-break-after: always;
                        }
                    }
                ";
            }
            $css .= "</style>";

            // Inject CSS
            if (strpos($html, '</head>') !== false) {
                $html = str_replace('</head>', $css . '</head>', $html);
            } else {
                $html = $css . $html;
            }

            if ($mode === 'web') {
                 // Check if body tag exists to avoid double wrapping or breaking structure
                if (strpos($html, '<body') !== false) {
                     // Inject class into body
                     $html = preg_replace('/<body([^>]*)>/', '<body$1 class="page-container">', $html);
                } else {
                     // No body tag, wrap it
                     $html = '<div class="page-container">' . $html . '</div>';
                }
            }
        }

        // 5. Image Paths (PDF needs absolute, Web needs relative/base64)
        if ($mode === 'pdf') {
            $html = preg_replace_callback('/<img[^>]+src="([^">]+)"/', function($matches) {
                $src = $matches[1];
                if (strpos($src, 'data:image') === 0) return $matches[0];
                
                $base_url = base_url();
                $src_clean = str_replace(['http://', 'https://'], '', $src);
                $base_clean = str_replace(['http://', 'https://'], '', $base_url);
                
                if (strpos($src_clean, $base_clean) !== false) {
                    $relative_path = str_replace($base_url, '', $src);
                    $relative_path = ltrim($relative_path, '/');
                    $file_path = FCPATH . $relative_path;
                    if (file_exists($file_path)) {
                        $type = pathinfo($file_path, PATHINFO_EXTENSION);
                        $data = file_get_contents($file_path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        return str_replace($src, $base64, $matches[0]);
                    }
                }
                return $matches[0];
            }, $html);
        }

        return $html;
    }
}
