const { createApp, ref, reactive, computed, onMounted, watch } = Vue;

// HTML escape helper to prevent XSS
const escapeHtml = (str) => {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
};

createApp({
    setup() {
        // --- State ---
        const rawConfig = ref(TEMPLATE_CONFIG);
        const templateHtml = ref(TEMPLATE_HTML);
        const templateId = ref(TEMPLATE_ID);
        const siteUrl = ref(SITE_URL);
        const archiveId = ref(ARCHIVE_ID);

        // Parse config - handle LEGACY (title/section-based) and NEW (flat) formats
        const templateVariables = computed(() => {
            if (!rawConfig.value || !Array.isArray(rawConfig.value)) return [];
            
            // Check if it's the LEGACY section-based format (using 'title' or 'section' key)
            if (rawConfig.value.length > 0 && (rawConfig.value[0].title || rawConfig.value[0].section) && rawConfig.value[0].fields) {
                // LEGACY FORMAT: Extract all fields from all sections
                const allFields = [];
                rawConfig.value.forEach(section => {
                    if (section.fields && Array.isArray(section.fields)) {
                        section.fields.forEach(field => {
                            if (field.variable && field.variable !== '_global_settings') {
                                allFields.push({
                                    variable: field.variable,
                                    label: field.label || field.variable,
                                    type: field.type || 'text'
                                });
                            }
                        });
                    }
                });
                return allFields;
            }
            
            // NEW FORMAT: Flat array of variables from Template Builder
            return rawConfig.value.filter(item => 
                item.variable && 
                item.variable !== '_global_settings' && 
                item.type !== 'settings'
            ).map(item => ({
                variable: item.variable,
                label: item.label || item.variable,
                type: item.type || 'text'
            }));
        });

        // Extract layout settings from config
        const templateLayout = computed(() => {
            if (!rawConfig.value || !Array.isArray(rawConfig.value)) return null;
            const settingsItem = rawConfig.value.find(item => item.variable === '_global_settings');
            return settingsItem?.layout || null;
        });

        // Form Data (Reactive)
        const formData = reactive({});
        const pejabatList = ref(typeof PEJABAT_DATA !== 'undefined' ? PEJABAT_DATA : []);

        // Mandatory Settings (Always present)
        const mandatorySettings = reactive({
            jumlah_salinan: 5,
            tampilkan_hijriah: true,
            tampilkan_nip: true,
            nomor_urut: 1
        });

        // Global Settings (Layout/Typography - Reactive & Persistent)
        const globalSettings = reactive({
            paperSize: 'A4',
            orientation: 'portrait',
            marginTop: 20,
            marginBottom: 20,
            marginLeft: 20,
            marginRight: 20,
            fontSize: '12pt',
            lineHeight: '1.5',
            showPageNumbers: false,
            defaultSkLogo: '',
            show_gelar: true,
            master_pengadilan: 'PENGADILAN AGAMA GORONTALO'
        });

        // Theme Logic
        const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');

        // UI State
        const isSidebarOpen = ref(false);
        const activeTab = ref('form');
        const isDataSectionOpen = ref(true);
        const isMandatorySectionOpen = ref(true);
        const isPenandatanganOpen = ref(true);
        const isSalinanOpen = ref(false);

        const toggleSidebar = () => {
            isSidebarOpen.value = !isSidebarOpen.value;
        };

        const toggleDataSection = () => {
            isDataSectionOpen.value = !isDataSectionOpen.value;
        };

        const toggleMandatorySection = () => {
            isMandatorySectionOpen.value = !isMandatorySectionOpen.value;
        };

        // Zoom Logic
        const zoomScale = ref(1.0);

        const zoomIn = () => {
            if (zoomScale.value < 2.0) {
                zoomScale.value = parseFloat((zoomScale.value + 0.1).toFixed(1));
            }
        };

        const zoomOut = () => {
            if (zoomScale.value > 0.5) {
                zoomScale.value = parseFloat((zoomScale.value - 0.1).toFixed(1));
            }
        };

        const resetZoom = () => {
            zoomScale.value = 1.0;
        };

        // Helper to set pejabat (auto-population)
        const setPejabat = (id) => {
            const p = pejabatList.value.find(x => x.id == id);
            if (p) {
                const safeJabatan = p.jabatan ? p.jabatan.trim() : '';

                formData.nama_penandatangan = p.nama;
                formData.nip_penandatangan = p.nip;
                formData.jabatan_penandatangan = safeJabatan.toUpperCase();
                formData['jabatan_penandatangan_select'] = p.jabatan;
            }
        };

        const saveAsDefault = () => {
            try {
                localStorage.setItem('sk_editor_settings', JSON.stringify(globalSettings));
                localStorage.setItem('sk_editor_mandatory', JSON.stringify(mandatorySettings));
                toastr.success('Settings saved as your default for new documents.', 'Defaults Saved');
            } catch (e) {
                console.error('Failed to save defaults:', e);
                toastr.error('Could not save settings to local storage.');
            }
        };

        const initializeSettings = () => {
            // 1. Load from LocalStorage first (User Defaults)
            const savedSettings = localStorage.getItem('sk_editor_settings');
            if (savedSettings) {
                try {
                    const parsed = JSON.parse(savedSettings);
                    Object.assign(globalSettings, parsed);
                } catch (e) { console.error('Settings parse error:', e); }
            }

            // Load mandatory settings defaults
            const savedMandatory = localStorage.getItem('sk_editor_mandatory');
            if (savedMandatory) {
                try {
                    const parsed = JSON.parse(savedMandatory);
                    Object.assign(mandatorySettings, parsed);
                } catch (e) { console.error('Mandatory settings parse error:', e); }
            }

            // 2. If Template has layout settings, apply them
            if (templateLayout.value) {
                const tl = templateLayout.value;
                if (tl.paperSize) globalSettings.paperSize = tl.paperSize;
                if (tl.orientation) globalSettings.orientation = tl.orientation;
                if (tl.margins) {
                    if (tl.margins.top !== undefined) globalSettings.marginTop = tl.margins.top;
                    if (tl.margins.bottom !== undefined) globalSettings.marginBottom = tl.margins.bottom;
                    if (tl.margins.left !== undefined) globalSettings.marginLeft = tl.margins.left;
                    if (tl.margins.right !== undefined) globalSettings.marginRight = tl.margins.right;
                }
            }

            // 3. If Draft Settings exist (Edit Mode), override
            if (DRAFT_SETTINGS && typeof DRAFT_SETTINGS === 'object' && Object.keys(DRAFT_SETTINGS).length > 0) {
                Object.assign(globalSettings, DRAFT_SETTINGS);
                // Load mandatory from draft if exists
                if (DRAFT_SETTINGS.mandatorySettings) {
                    Object.assign(mandatorySettings, DRAFT_SETTINGS.mandatorySettings);
                }
            }

            // Apply Default SK Content Logo (if New Draft and global setting exists)
            if (!DRAFT_DATA && globalSettings.defaultSkLogo && !formData.skContentLogo) {
                formData.skContentLogo = globalSettings.defaultSkLogo;
                if (!formData.skContentLogoWidth) formData.skContentLogoWidth = 100;
            }
        };

        // --- Initialization ---
        onMounted(() => {
            // Apply initial theme
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Initialize formData from template variables
            templateVariables.value.forEach(field => {
                if (field.type === 'repeater') {
                    formData[field.variable] = [];
                } else if (field.variable === 'no_sk' && typeof TEMPLATE_PATTERN !== 'undefined' && TEMPLATE_PATTERN) {
                    formData[field.variable] = TEMPLATE_PATTERN;
                } else {
                    formData[field.variable] = '';
                }
            });

            // Initialize Salinan if not present
            if (!formData.salinan) {
                formData.salinan = [];
            }

            // Initialize Attachments if not present
            if (!formData.attachments) {
                formData.attachments = [];
            }

            // Overwrite with saved draft data if it exists
            if (DRAFT_DATA) {
                Object.assign(formData, DRAFT_DATA);
                // Load mandatory settings from draft
                if (DRAFT_DATA.mandatorySettings) {
                    Object.assign(mandatorySettings, DRAFT_DATA.mandatorySettings);
                }
            } else {
                // NEW DRAFT: Attempt to set Default Pejabat
                const defaultPejabat = pejabatList.value.find(p => p.is_default == 1);
                if (defaultPejabat) {
                    setPejabat(defaultPejabat.id);
                }
            }

            // Load Global Settings
            initializeSettings();
        });

        // --- Watchers for Smart Logic ---
        // Date Logic (Indo + Hijri)
        watch(() => formData.tanggal_sk, (newVal) => {
            if (newVal) {
                const date = new Date(newVal);

                const indoFormatter = new Intl.DateTimeFormat('id-ID', {
                    day: 'numeric', month: 'long', year: 'numeric'
                });
                formData.tanggal_indo = indoFormatter.format(date);

                // Only set hijri if toggle is on
                if (mandatorySettings.tampilkan_hijriah) {
                    const hijriFormatter = new Intl.DateTimeFormat('id-ID-u-ca-islamic', {
                        day: 'numeric', month: 'long', year: 'numeric'
                    });
                    let hijri = hijriFormatter.format(date);
                    hijri = hijri.replace(' AH', ' H');
                    formData.tanggal_hijri = hijri;
                } else {
                    formData.tanggal_hijri = '';
                }
            } else {
                formData.tanggal_indo = '';
                formData.tanggal_hijri = '';
            }
        }, { immediate: true });

        // Watch for hijriah toggle changes
        watch(() => mandatorySettings.tampilkan_hijriah, (newVal) => {
            if (!newVal) {
                formData.tanggal_hijri = '';
            } else if (formData.tanggal_sk) {
                // Recalculate hijri date when toggle is turned on
                const date = new Date(formData.tanggal_sk);
                const hijriFormatter = new Intl.DateTimeFormat('id-ID-u-ca-islamic', {
                    day: 'numeric', month: 'long', year: 'numeric'
                });
                let hijri = hijriFormatter.format(date);
                hijri = hijri.replace(' AH', ' H');
                formData.tanggal_hijri = hijri;
            }
        });

        // Logo Tengah Width Synchronization
        watch(() => formData.logo_tengah_width, (newVal) => {
            if (newVal && formData.skContentLogoWidth !== newVal) {
                formData.skContentLogoWidth = newVal;
            }
        });

        watch(() => formData.skContentLogoWidth, (newVal) => {
            if (newVal && formData.logo_tengah_width !== newVal) {
                formData.logo_tengah_width = newVal;
            }
        });

        // --- Computed Properties ---
        const previewHtml = computed(() => {
            let html = templateHtml.value;

            // Inject SK Content Logo
            if (formData.skContentLogo) {
                try {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const width = formData.skContentLogoWidth || 100;
                    const logoDiv = doc.createElement('div');
                    logoDiv.style.width = '100%';
                    logoDiv.style.marginTop = '0px';
                    logoDiv.style.marginBottom = '15px';
                    logoDiv.style.textAlign = 'center';
                    logoDiv.style.clear = 'both';
                    logoDiv.style.position = 'relative';
                    logoDiv.style.display = 'block';

                    const img = doc.createElement('img');
                    img.src = formData.skContentLogo;
                    img.style.width = width + 'px';
                    img.style.height = 'auto';
                    img.style.display = 'inline-block';

                    logoDiv.appendChild(img);

                    if (doc.body.firstChild) {
                        doc.body.insertBefore(logoDiv, doc.body.firstChild);
                    } else {
                        doc.body.appendChild(logoDiv);
                    }

                    html = doc.body.innerHTML;
                } catch (e) {
                    console.error("Auto-inject logo failed:", e);
                    const logoHtml = '<div style="text-align: center; width: 100%; margin: 0 0 15px 0;"><img src="' + formData.skContentLogo + '" style="width: ' + (formData.skContentLogoWidth || 100) + 'px;"></div>';
                    html = logoHtml + html;
                }
            }

            // Simple Replacements (FormData)
            const cleanName = (fullName) => {
                if (!fullName) return '';
                let name = fullName;
                // Remove front titles iteratively (e.g. Drs. H.)
                while (name.match(/^[A-Za-z]+\.\s*/)) {
                    name = name.replace(/^[A-Za-z]+\.\s*/, '');
                }
                // Remove back titles (everything after the first comma)
                name = name.replace(/,.*$/, '');
                return name.trim();
            };

            // --- Pre-process Headers (KEPUTUSAN & TENTANG) ---
            // We want the top headers to have the full title (e.g. KETUA PENGADILAN AGAMA GORONTALO)
            // But the signature at the bottom to only have the short title (e.g. KETUA)
            const jabatanUpperPre = (formData.jabatan_penandatangan || '').toUpperCase();
            const masterPengadilanPre = (globalSettings.master_pengadilan || '').toUpperCase();
            const gabunganJabatanPre = (jabatanUpperPre + ' ' + masterPengadilanPre).trim();

            if (gabunganJabatanPre) {
                // Replace {{jabatan_penandatangan}} that appear closely after KEPUTUSAN or TENTANG
                html = html.replace(/(KEPUTUSAN[\s\S]{0,150}?){{jabatan_penandatangan}}/i, '$1' + gabunganJabatanPre);
                html = html.replace(/(TENTANG[\s\S]{0,150}?){{jabatan_penandatangan}}/i, '$1' + gabunganJabatanPre);
            }

            for (let [key, value] of Object.entries(formData)) {
                if (Array.isArray(value)) continue;

                if (key === 'nama_penandatangan' && !globalSettings.show_gelar) {
                    value = cleanName(value);
                }

                // Handle hide NIP based on mandatory settings
                if (key === 'nip_penandatangan' && !mandatorySettings.tampilkan_nip) {
                    const regex = new RegExp('{{' + key + '}}', 'g');
                    html = html.replace(regex, '');
                    continue;
                }

                // Handle hide Hijri date based on mandatory settings
                if (key === 'tanggal_hijri' && !mandatorySettings.tampilkan_hijriah) {
                    const regex = new RegExp('{{' + key + '}}', 'g');
                    html = html.replace(regex, '');
                    continue;
                }

                const regex = new RegExp('{{' + key + '}}', 'g');
                const formattedValue = escapeHtml(value).replace(/\n/g, '<br>');
                html = html.replace(regex, formattedValue);
            }

            // Clean up dangling "NIP. " if tampilkan_nip is off
            if (!mandatorySettings.tampilkan_nip) {
                html = html.replace(/NIP\.\s*(?=<)/g, '');
                html = html.replace(/NIP\.\s*$/gm, '');
            }

            // Handle Hijri date visibility - remove entire <p> containing hijri if off
            if (!mandatorySettings.tampilkan_hijriah) {
                // Remove entire <p> tag that contains only {{tanggal_hijri}} or is now empty
                html = html.replace(/<p[^>]*>\s*{{tanggal_hijri}}\s*<\/p>/g, '');
                html = html.replace(/{{tanggal_hijri}}/g, '');
                // Also remove common Hijri wrappers
                html = html.replace(/\s*\/\s*{{tanggal_hijri}}/g, '');
                html = html.replace(/\({{tanggal_hijri}}\)/g, '');
                // Remove empty <p> tags that may remain
                html = html.replace(/<p[^>]*>\s*<\/p>/g, '');
            }

            // Replace mandatory settings variables
            html = html.replace(/{{jumlah_salinan}}/g, mandatorySettings.jumlah_salinan);
            html = html.replace(/{{nomor_urut}}/g, mandatorySettings.nomor_urut);

            // Global Settings Replacements
            for (const [key, value] of Object.entries(globalSettings)) {
                const regex = new RegExp('{{globalSettings.' + key + '}}', 'g');
                html = html.replace(regex, value);
            }

            // Repeater Logic
            templateVariables.value.forEach(field => {
                if (field.type === 'repeater') {
                    const items = formData[field.variable] || [];
                    const loopRegex = new RegExp('{{#each ' + field.variable + '}}([\\s\\S]*?){{/each}}', 'g');

                    html = html.replace(loopRegex, (match, content) => {
                        return items.map(item => {
                            const formattedItem = escapeHtml(item).replace(/\n/g, '<br>');
                            return content.replace(/{{this}}/g, formattedItem);
                        }).join('');
                    });
                }
            });

            // Diktum (KESATU, KEDUA, KETIGA, ...) rendering
            const diktumLabels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH', 'KESEBELAS', 'KEDUA BELAS', 'KETIGA BELAS', 'KEEMPAT BELAS', 'KELIMA BELAS'];
            const diktumItems = formData.list_diktum || [];
            
            // Build the "Menetapkan : KEPUTUSAN ... TENTANG ..." header auto text
            const jabatanUpper = (formData.jabatan_penandatangan || '').toUpperCase();
            const masterPengadilan = (globalSettings.master_pengadilan || '').toUpperCase();
            const judulUpper = (formData.judul_sk || '').toUpperCase();
            const menetapkanHeader = '<strong>KEPUTUSAN ' + jabatanUpper + ' ' + masterPengadilan + ' TENTANG ' + judulUpper + '</strong>';
            
            // Replace placeholder with header + diktum items
            if (diktumItems.length > 0) {
                let diktumHtml = menetapkanHeader;
                diktumItems.forEach((item, idx) => {
                    const label = diktumLabels[idx] || 'KE-' + (idx + 1);
                    const formattedItem = escapeHtml(item).replace(/\n/g, '<br>');
                    // Each diktum item gets its own table row (KESATU, KEDUA, etc.)
                    diktumHtml += '</td></tr></tbody></table>';
                    diktumHtml += '<table style="width: 100%; border: none; margin-bottom: 5px;"><tbody>';
                    diktumHtml += '<tr>';
                    diktumHtml += '<td style="width: 120px; vertical-align: top; border: none; padding: 5px 0; font-weight: bold;">' + label + '</td>';
                    diktumHtml += '<td style="width: 20px; vertical-align: top; border: none; padding: 5px 0;">:</td>';
                    diktumHtml += '<td style="vertical-align: top; border: none; padding: 5px 0;">' + formattedItem + '</td>';
                    diktumHtml += '</tr>';
                });
                html = html.replace('{{diktum_placeholder}}', diktumHtml);
            } else {
                // No diktum items yet, show just the header
                html = html.replace('{{diktum_placeholder}}', menetapkanHeader);
            }

            // Conditional Logic
            const ifRegex = /{{#if\s+(.*?)}}([\s\S]*?){{\/if}}/g;
            html = html.replace(ifRegex, (match, variable, content) => {
                if (formData[variable]) return content;
                if (globalSettings[variable]) return content;
                if (mandatorySettings[variable]) return content;
                return '';
            });

            // Inject Attachments
            if (formData.attachments && formData.attachments.length > 0) {
                const toRoman = (num) => {
                    const lookup = { M: 1000, CM: 900, D: 500, CD: 400, C: 100, XC: 90, L: 50, XL: 40, X: 10, IX: 9, V: 5, IV: 4, I: 1 };
                    let roman = '';
                    for (let i in lookup) {
                        while (num >= lookup[i]) {
                            roman += i;
                            num -= lookup[i];
                        }
                    }
                    return roman;
                };

                const totalAtth = formData.attachments.length;

                formData.attachments.forEach((att, index) => {
                    const noSK = formData.no_sk || '...';
                    const tanggalIndo = formData.tanggal_indo || '...';
                    const pejabatJabatan = (formData.jabatan_penandatangan || 'PEJABAT').toUpperCase();

                    let lampiranLabel = 'LAMPIRAN';
                    if (totalAtth > 1) {
                        lampiranLabel += ' ' + toRoman(index + 1);
                    }

                    const lampiranHtml = '\n                        <div class="smart-attachment-break" data-title="' + (att.title || 'Lampiran') + '"></div>\n                        <div class="attachment-header" style="float: right; text-align: left; width: 75%; margin-bottom: 20px; font-size: 10pt; line-height: 1.2;">\n                            <table>\n                                <tr>\n                                    <td style="vertical-align: top; white-space: nowrap; width: 1%;">' + lampiranLabel + '</td>\n                                    <td style="vertical-align: top; width: 1%; padding: 0 5px;">:</td>\n                                    <td style="vertical-align: top;">KEPUTUSAN ' + pejabatJabatan + '<br>TENTANG ' + (formData.judul_sk || formData.tentang || '').toUpperCase() + '</td>\n                                </tr>\n                                <tr>\n                                    <td style="vertical-align: top;">NOMOR</td>\n                                    <td style="vertical-align: top;">:</td>\n                                    <td>' + noSK + '</td>\n                                </tr>\n                                <tr>\n                                    <td style="vertical-align: top;">TANGGAL</td>\n                                    <td style="vertical-align: top;">:</td>\n                                    <td>' + tanggalIndo + '</td>\n                                </tr>\n                            </table>\n                        </div>\n                        <div style="clear: both;"></div>\n                        <div class="attachment-content">\n                            ' + (att.content || '') + '\n                        </div>\n                    ';
                    html += lampiranHtml;
                });
            }

            // Inject Salinan / Tembusan section at the bottom
            if (formData.salinan && formData.salinan.length > 0) {
                const filteredSalinan = formData.salinan.filter(s => s && s.trim());
                if (filteredSalinan.length > 0) {
                    let salinanHtml = '<div style="margin-top: 40px;">';
                    salinanHtml += '<p style="margin: 0 0 5px 0;">SALINAN Keputusan ini disampaikan kepada:</p>';
                    filteredSalinan.forEach((s, idx) => {
                        salinanHtml += '<p style="margin: 0; padding-left: 2em; text-indent: -1.5em;">' + (idx + 1) + '.&nbsp;&nbsp;' + escapeHtml(s) + '</p>';
                    });
                    salinanHtml += '</div>';
                    html += salinanHtml;
                }
            }

            return html;
        });

        const paperStyle = computed(() => {
            const width = globalSettings.orientation === 'landscape' ? '297mm' : '210mm';
            const minHeight = globalSettings.orientation === 'landscape' ? '210mm' : '297mm';
            return { width, minHeight, padding: '0' };
        });

        // Field type helper
        const getFieldInputType = (type) => {
            const typeMap = {
                'text': 'textarea',
                'textarea': 'textarea',
                'number': 'number',
                'date': 'date',
                'select': 'select',
                'checkbox': 'checkbox',
                'image': 'image',
                'repeater': 'repeater'
            };
            return typeMap[type] || 'textarea';
        };

        const addRepeaterItem = (variable) => {
            if (!formData[variable]) formData[variable] = [];
            formData[variable].push('');
        };

        const removeRepeaterItem = (variable, index) => {
            if (formData[variable]) formData[variable].splice(index, 1);
        };

        const addAttachment = () => {
            if (!formData.attachments) formData.attachments = [];
            formData.attachments.push({ title: 'Lampiran ...', content: '' });

            const index = formData.attachments.length - 1;
            const id = 'attachment-editor-' + index;
            Vue.nextTick(() => {
                initTinyMCE(id, index);
            });
        };

        const addSalinan = () => {
            if (!formData.salinan) formData.salinan = [];
            formData.salinan.push('');
        };

        const removeSalinan = (index) => {
            if (formData.salinan) formData.salinan.splice(index, 1);
        };

        const removeAttachment = (index) => {
            const id = 'attachment-editor-' + index;
            if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                tinymce.get(id).remove();
            }
            formData.attachments.splice(index, 1);
        };

        const initTinyMCE = (id, index) => {
            if (typeof tinymce === 'undefined') {
                console.error("TinyMCE not loaded");
                return;
            }

            setTimeout(() => {
                const isDark = document.documentElement.classList.contains('dark');

                tinymce.init({
                    selector: '#' + id,
                    menubar: false,
                    statusbar: false,
                    height: 500,
                    skin: isDark ? 'oxide-dark' : 'oxide',
                    content_css: isDark ? 'dark' : 'default',
                    plugins: 'table lists advlist',
                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | table',
                    content_style: "@font-face { font-family: 'Bookman Old Style'; src: url('" + siteUrl.value + "assets/BOOKOS.TTF') format('truetype'); font-weight: normal; } @font-face { font-family: 'Bookman Old Style'; src: url('" + siteUrl.value + "assets/BOOKOSB.TTF') format('truetype'); font-weight: bold; } body { font-family: 'Bookman Old Style', serif; font-size:12pt; } table { width: 100% !important; border-collapse: collapse; } td, th { border: 1px solid #000; padding: 4px; vertical-align: top; }",
                    setup: (editor) => {
                        editor.on('init', () => {
                            if (formData.attachments[index] && formData.attachments[index].content) {
                                editor.setContent(formData.attachments[index].content);
                            }
                        });

                        editor.on('Change Keyup', () => {
                            if (formData.attachments[index]) {
                                formData.attachments[index].content = editor.getContent();
                            }
                        });
                    }
                });
            }, 100);
        };

        const compressImage = (file, maxWidth, maxHeight, quality, callback) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    let dataUrl = canvas.toDataURL('image/webp', quality);

                    if (dataUrl.length > 500000) {
                        const scale = 0.7;
                        canvas.width = width * scale;
                        canvas.height = height * scale;
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        dataUrl = canvas.toDataURL('image/webp', quality);
                    }

                    callback(dataUrl);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        };

        const handleGenericImageUpload = (event, variable, widthVariable, defaultWidth) => {
            const file = event.target.files[0];
            if (file) {
                compressImage(file, 300, 300, 0.7, (dataUrl) => {
                    formData[variable] = dataUrl;
                    if (!formData[widthVariable]) {
                        formData[widthVariable] = defaultWidth || 70;
                    }
                    event.target.value = '';
                });
            }
        };

        const handleContentLogoUpload = (event) => {
            const file = event.target.files[0];
            if (file) {
                compressImage(file, 300, 300, 0.7, (dataUrl) => {
                    formData.skContentLogo = dataUrl;
                    formData.logo_tengah = dataUrl;

                    if (!formData.skContentLogoWidth) formData.skContentLogoWidth = 100;
                    if (!formData.logo_tengah_width) formData.logo_tengah_width = 100;

                    event.target.value = '';
                });
            }
        };

        const recompressBase64 = (base64, maxWidth, maxHeight, quality) => {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    const newDataUrl = canvas.toDataURL('image/webp', quality);
                    resolve(newDataUrl);
                };
                img.onerror = () => resolve(base64);
                img.src = base64;
            });
        };

        const sanitizeGlobalSettings = async () => {
            if (globalSettings.defaultSkLogo && globalSettings.defaultSkLogo.length > 500000) {
                toastr.info("Optimizing Default SK Logo...", { timeOut: 2000 });
                globalSettings.defaultSkLogo = await recompressBase64(globalSettings.defaultSkLogo, 300, 300, 0.7);
                localStorage.setItem('sk_editor_settings', JSON.stringify(globalSettings));
            }
        };

        const isSaving = ref(false);

        const saveDraft = async () => {
            if (isSaving.value) return;
            isSaving.value = true;
            try {
                await sanitizeGlobalSettings();

                // Include mandatory settings in the save data
                const saveData = { ...formData, mandatorySettings: { ...mandatorySettings } };
                const saveSettings = { ...globalSettings, mandatorySettings: { ...mandatorySettings } };

                const payloadSize = new Blob([JSON.stringify(saveData) + JSON.stringify(saveSettings)]).size;
                if (payloadSize > 950000) {
                    const sizeMB = (payloadSize / 1024 / 1024).toFixed(2);
                    toastr.error('Data too large (' + sizeMB + 'MB). Limit is ~0.95MB. Please compress images.');
                    isSaving.value = false;
                    return;
                }

                const response = await fetch(siteUrl.value + 'sk_editor/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        data: JSON.stringify(saveData),
                        settings: JSON.stringify(saveSettings),
                        template_id: templateId.value,
                        archive_id: archiveId.value || ''
                    })
                });

                const text = await response.text();
                let res;
                try {
                    res = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON:', text);
                    toastr.error('Server Error: ' + text.substring(0, 50));
                    return;
                }

                if (res.status === 'success') {
                    toastr.success('Draft Saved Successfully!');
                    archiveId.value = res.id;
                } else {
                    toastr.error('Error saving draft: ' + (res.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Save error:', error);
                toastr.error('Failed to save draft.');
            } finally {
                isSaving.value = false;
            }
        };

        const getPageSizeCSS = () => {
            const size = globalSettings.paperSize;
            const orientation = globalSettings.orientation;

            let width, height;

            if (size === 'A4') {
                width = '210mm'; height = '297mm';
            } else if (size === 'F4') {
                width = '215mm'; height = '330mm';
            } else {
                width = '210mm'; height = '297mm';
            }

            if (orientation === 'landscape') {
                return height + ' ' + width;
            }
            return width + ' ' + height;
        };

        const printPdf = () => {
            const sizeCSS = getPageSizeCSS();
            const [width, height] = sizeCSS.split(' ');

            const style = document.createElement('style');
            style.id = 'dynamic-print-style';
            style.innerHTML = `
                @page {
                    size: ${sizeCSS};
                    margin: 0;
                }
                @media print {
                    html, body {
                        width: ${width} !important;
                        height: auto !important;
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    
                    #pagination-container {
                        width: ${width} !important;
                    }
                    
                    .paper-page {
                        width: ${width} !important;
                        height: ${height} !important;
                        min-height: ${height} !important;
                        max-height: ${height} !important;
                        page-break-after: always !important;
                        break-after: page !important;
                        page-break-inside: avoid !important;
                        break-inside: avoid !important;
                    }
                    
                    .paper-page:last-child {
                        page-break-after: auto !important;
                        break-after: auto !important;
                    }
                }
            `;
            document.head.appendChild(style);

            window.print();

            setTimeout(() => {
                const el = document.getElementById('dynamic-print-style');
                if (el) el.remove();
            }, 1000);
        };

        const exportWord = () => {
            const content = document.getElementById('raw-content').innerHTML;
            const sizeCSS = getPageSizeCSS();

            const preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>\n            <head><meta charset='utf-8'><title>Export Word</title>\n            <style>\n                body { font-family: 'Bookman Old Style', serif; font-size: 12pt; margin: 0; padding: 0; }\n                table { border-collapse: collapse; width: 100%; }\n                td { vertical-align: top; padding: 0; }\n                p { margin: 0; padding: 0; }\n                ul, ol { margin: 0 0 0.5em 0; padding-left: 2em; }\n                li { display: list-item; margin-bottom: 0.25em; }\n                .text-center { text-align: center; }\n                .text-right { text-align: right; }\n                .font-bold { font-weight: bold; }\n                .uppercase { text-transform: uppercase; }\n                .underline { text-decoration: underline; }\n                @page Section1 {\n                    size: " + sizeCSS + ";\n                    mso-page-orientation: " + globalSettings.orientation + ";\n                    margin: " + globalSettings.marginTop + "mm " + globalSettings.marginRight + "mm " + globalSettings.marginBottom + "mm " + globalSettings.marginLeft + "mm;\n                }\n                div.Section1 { page: Section1; }\n            </style>\n            </head><body><div class=\"Section1\">";
            const postHtml = "</div></body></html>";

            const html = preHtml + content + postHtml;

            const url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);

            const link = document.createElement('a');
            link.href = url;
            link.download = 'SK_' + (formData.no_sk ? formData.no_sk.replace(/[^a-z0-9]/gi, '_') : 'Draft') + '.doc';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        const exportPdf = () => {
            alert("To ensure the PDF matches the editor EXACTLY, please select 'Save as PDF' in the destination dropdown.");
            printPdf();
        };

        const fixAutoFormatting = () => {
            const container = document.getElementById('raw-content');
            if (!container) return;

            const tds = container.querySelectorAll('td');
            tds.forEach(td => {
                const text = td.textContent.trim();
                if (/^menimbang/i.test(text)) {
                    let sibling = td.nextElementSibling;
                    while (sibling) {
                        const list = sibling.querySelector('ul, ol');
                        if (list) {
                            list.style.listStyleType = 'lower-alpha';
                            if (list.tagName === 'OL') list.setAttribute('type', 'a');
                            const items = list.querySelectorAll('li');
                            items.forEach(li => li.style.display = 'list-item');
                        }
                        sibling = sibling.nextElementSibling;
                    }
                }
                if (/^mengingat/i.test(text)) {
                    let sibling = td.nextElementSibling;
                    while (sibling) {
                        const list = sibling.querySelector('ul, ol');
                        if (list) {
                            list.style.listStyleType = 'decimal';
                            if (list.tagName === 'OL') list.setAttribute('type', '1');
                        }
                        sibling = sibling.nextElementSibling;
                    }
                }
            });
        };

        const toggleTheme = () => {
            isDarkMode.value = !isDarkMode.value;
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('sk_editor_theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('sk_editor_theme', 'light');
            }
        };

        const paginate = () => {
            const rawContainer = document.getElementById('raw-content');
            const outputContainer = document.getElementById('pagination-container');
            if (!rawContainer || !outputContainer) return;

            outputContainer.innerHTML = '';

            const mmToPx = 3.78;
            let pageHeightMm = 297;
            let pageWidthMm = 210;

            if (globalSettings.paperSize === 'F4') {
                pageHeightMm = 330; pageWidthMm = 215;
            }
            if (globalSettings.orientation === 'landscape') {
                [pageHeightMm, pageWidthMm] = [pageWidthMm, pageHeightMm];
            }

            const pageHeightPx = pageHeightMm * mmToPx;
            const marginTopPx = (globalSettings.marginTop || 20) * mmToPx;
            const marginBottomPx = (globalSettings.marginBottom || 20) * mmToPx;
            const contentHeightPx = pageHeightPx - marginTopPx - marginBottomPx;

            let pageCount = 0;
            let currentContent = null;

            const createPage = () => {
                pageCount++;
                const page = document.createElement('div');
                page.className = 'paper-page bg-white shadow-lg relative';
                page.style.width = pageWidthMm + 'mm';
                page.style.height = pageHeightMm + 'mm';
                page.style.padding = globalSettings.marginTop + 'mm ' + globalSettings.marginRight + 'mm ' + globalSettings.marginBottom + 'mm ' + globalSettings.marginLeft + 'mm';
                page.style.fontSize = globalSettings.fontSize || '12pt';
                page.style.lineHeight = globalSettings.lineHeight || '1.5';
                page.style.fontFamily = "'Bookman Old Style', serif";

                page.dataset.pageNum = pageCount;

                const content = document.createElement('div');
                content.className = 'page-content';
                content.style.width = '100%';
                content.style.height = '100%';
                content.style.overflow = 'hidden';

                page.appendChild(content);
                outputContainer.appendChild(page);

                currentContent = content;
                return content;
            };

            createPage();

            const hasOverflow = (container) => {
                return container.scrollHeight > container.clientHeight + 1;
            };

            const processNode = (node) => {
                if (node.nodeType === Node.TEXT_NODE && !node.textContent.trim()) return;

                if (node.nodeType === Node.ELEMENT_NODE && node.classList.contains('smart-attachment-break')) {
                    createPage();
                    return;
                }

                currentContent.appendChild(node);

                if (hasOverflow(currentContent)) {
                    currentContent.removeChild(node);

                    const tag = node.tagName;

                    if (tag === 'TABLE') {
                        const tbody = node.querySelector('tbody') || node;
                        const trs = Array.from(tbody.children).filter(n => n.tagName === 'TR');

                        const tableHeader = node.cloneNode(false);
                        tableHeader.style.marginBottom = '0';
                        tableHeader.style.borderBottom = 'none';
                        const tbodyPart1 = document.createElement('tbody');
                        tableHeader.appendChild(tbodyPart1);

                        currentContent.appendChild(tableHeader);

                        const remainingTrs = [];

                        trs.forEach(tr => {
                            tbodyPart1.appendChild(tr);
                            if (hasOverflow(currentContent)) {
                                tbodyPart1.removeChild(tr);
                                remainingTrs.push(tr);
                            }
                        });

                        if (remainingTrs.length > 0) {
                            createPage();

                            const tablePart2 = node.cloneNode(false);
                            const tbodyPart2 = document.createElement('tbody');
                            tablePart2.appendChild(tbodyPart2);
                            remainingTrs.forEach(tr => tbodyPart2.appendChild(tr));
                            processNode(tablePart2);
                        }

                    } else if (tag === 'UL' || tag === 'OL') {
                        const lis = Array.from(node.children);
                        const listPart1 = node.cloneNode(false);
                        currentContent.appendChild(listPart1);

                        const remainingLis = [];

                        lis.forEach(li => {
                            listPart1.appendChild(li);
                            if (hasOverflow(currentContent)) {
                                listPart1.removeChild(li);
                                remainingLis.push(li);
                            }
                        });

                        if (remainingLis.length > 0) {
                            createPage();
                            const listPart2 = node.cloneNode(false);
                            remainingLis.forEach(li => listPart2.appendChild(li));
                            processNode(listPart2);
                        }
                    } else {
                        createPage();
                        currentContent.appendChild(node);
                    }
                }
            };

            const sourceNodes = Array.from(rawContainer.children[0].cloneNode(true).childNodes);
            sourceNodes.forEach(node => processNode(node));

            if (globalSettings.showPageNumbers) {
                const pages = outputContainer.querySelectorAll('.paper-page');
                const totalPages = pages.length;
                pages.forEach((page, index) => {
                    const pageNum = index + 1;
                    const footer = document.createElement('div');
                    footer.className = 'page-footer absolute text-xs text-gray-500';
                    footer.style.bottom = (globalSettings.marginBottom / 2) + 'mm';
                    footer.style.right = globalSettings.marginRight + 'mm';
                    footer.style.fontFamily = 'Arial, sans-serif';
                    footer.innerText = 'Halaman ' + pageNum + ' dari ' + totalPages;
                    page.appendChild(footer);
                });
            }
        };

        // Watchers for Paging
        watch(previewHtml, () => {
            Vue.nextTick(() => {
                fixAutoFormatting();
                paginate();
            });
        });

        watch(() => globalSettings.paperSize, () => Vue.nextTick(paginate));
        watch(() => globalSettings.orientation, () => Vue.nextTick(paginate));
        watch(() => globalSettings.marginTop, () => Vue.nextTick(paginate));
        watch(() => globalSettings.marginBottom, () => Vue.nextTick(paginate));
        watch(() => globalSettings.fontSize, () => Vue.nextTick(paginate));
        watch(() => globalSettings.lineHeight, () => Vue.nextTick(paginate));
        watch(() => globalSettings.showPageNumbers, () => Vue.nextTick(paginate));

        // Initial Pagination
        onMounted(() => {
            setTimeout(() => {
                fixAutoFormatting();
                paginate();

                if (formData.attachments && formData.attachments.length > 0) {
                    formData.attachments.forEach((_, index) => {
                        const id = 'attachment-editor-' + index;
                        initTinyMCE(id, index);
                    });
                }
            }, 500);
        });

        return {
            templateVariables,
            formData,
            mandatorySettings,
            globalSettings,
            isDarkMode,
            toggleTheme,
            previewHtml,
            paperStyle,
            getFieldInputType,
            addRepeaterItem,
            removeRepeaterItem,
            addAttachment,
            removeAttachment,
            addSalinan,
            removeSalinan,
            saveDraft,
            isSaving,
            printPdf,
            exportWord,
            exportPdf,
            fixAutoFormatting,
            pejabatList,
            setPejabat,
            paginate,
            handleContentLogoUpload,
            handleGenericImageUpload,
            isSidebarOpen,
            toggleSidebar,
            isDataSectionOpen,
            toggleDataSection,
            isMandatorySectionOpen,
            toggleMandatorySection,
            isPenandatanganOpen,
            isSalinanOpen,
            zoomScale,
            zoomIn,
            zoomOut,
            resetZoom,
            saveAsDefault,
            activeTab
        };
    }
}).mount('#app');
