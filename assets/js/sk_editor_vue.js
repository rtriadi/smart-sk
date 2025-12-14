const { createApp, ref, reactive, computed, onMounted, watch } = Vue;

createApp({
    setup() {
        // --- State ---
        const config = ref(TEMPLATE_CONFIG);
        // Filter out legacy 'jabatan_selector' from dynamic form if it exists, 
        // as we now use the dedicated "Penandatangan" sidebar section.
        if (config.value && Array.isArray(config.value)) {
            config.value.forEach(section => {
                if (section.fields) {
                    section.fields = section.fields.filter(f => f.variable !== 'jabatan_selector');
                }
            });
        }
        const templateHtml = ref(TEMPLATE_HTML);
        const templateId = ref(TEMPLATE_ID);
        const siteUrl = ref(SITE_URL);
        const archiveId = ref(ARCHIVE_ID);

        // Form Data (Reactive)
        const formData = reactive({});
        const pejabatList = ref(typeof PEJABAT_DATA !== 'undefined' ? PEJABAT_DATA : []);

        // Global Settings (Reactive & Persistent)
        const globalSettings = reactive({
            paperSize: 'A4', // A4, F4, Legal
            orientation: 'portrait', // portrait, landscape
            marginTop: 20,
            marginBottom: 20,
            marginLeft: 25,
            marginRight: 20,
            showKop: true,
            // Typography
            fontSize: '12pt',
            lineHeight: '1.5',
            // Kop Surat Fields
            // Kop Surat Fields
            kopLogo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/Logo_of_the_Ministry_of_Religious_Affairs_of_the_Republic_of_Indonesia.svg/1200px-Logo_of_the_Ministry_of_Religious_Affairs_of_the_Republic_of_Indonesia.svg.png',
            kopTitle1: 'MAHKAMAH AGUNG REPUBLIK INDONESIA',
            kopTitle2: 'DIREKTORAT JENDERAL BADAN PERADILAN AGAMA',
            kopTitle3: 'PENGADILAN TINGGI AGAMA GORONTALO',
            kopTitle4: 'PENGADILAN AGAMA GORONTALO',
            kopAddress: 'Jalan Achmad Nadjamuddin No.22, Dulalowo Timur, Kecamatan Kota Tengah\nKota Gorontalo, 96138. www.pa-gorontalo.go.id, surat@pa-gorontalo.go.id',
            kopLogoWidth: 100, // Default width in px
            showPageNumbers: false, // Page Numbering Toggle
            defaultSkLogo: ''
        });

        // Theme Logic
        const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');

        const selectedPejabatId = ref('');

        // Helper to set pejabat
        const setPejabat = (id) => {
            selectedPejabatId.value = id; // Sync dropdown
            const p = pejabatList.value.find(x => x.id == id);
            if (p) {
                // Determine full title
                let jabatanFull = p.jabatan;

                // Map short names to long names (Legacy Support / Consistency)
                const titles = {
                    'Ketua': 'KETUA PENGADILAN AGAMA GORONTALO',
                    'Wakil Ketua': 'WAKIL KETUA PENGADILAN AGAMA GORONTALO',
                    'Panitera': 'PANITERA PENGADILAN AGAMA GORONTALO',
                    'Sekretaris': 'SEKRETARIS PENGADILAN AGAMA GORONTALO'
                };

                // Flexible Match (Case insensitive check if direct match fails)
                if (titles[p.jabatan]) {
                    jabatanFull = titles[p.jabatan];
                } else {
                    // Try finding key case-insensitive
                    const key = Object.keys(titles).find(k => k.toLowerCase() === p.jabatan.toLowerCase());
                    if (key) jabatanFull = titles[key];
                }

                formData.nama_penandatangan = p.nama;
                formData.nip_penandatangan = p.nip;
                formData.jabatan_penandatangan = jabatanFull;
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

            // Initialize formData
            config.value.forEach(section => {
                section.fields.forEach(field => {
                    if (field.type === 'repeater') {
                        formData[field.variable] = [];
                    } else if (field.variable === 'no_sk' && typeof TEMPLATE_PATTERN !== 'undefined' && TEMPLATE_PATTERN) {
                        // Prefer the database pattern for the letter number
                        formData[field.variable] = TEMPLATE_PATTERN;
                    } else {
                        formData[field.variable] = field.default || '';
                    }
                });
            });

            // Initialize Attachments if not present
            if (!formData.attachments) {
                formData.attachments = [];
            }

            // Then overwrite with saved draft data if it exists
            if (DRAFT_DATA) {
                // Only overwrite fields that exist in DRAFT_DATA
                // This preserves new fields that might not be in the draft
                Object.assign(formData, DRAFT_DATA);

                // EDIT MODE: Restore Signatory Dropdown Selection
                if (formData.nip_penandatangan) {
                    const match = pejabatList.value.find(p => p.nip == formData.nip_penandatangan);
                    if (match) selectedPejabatId.value = match.id;
                } else if (formData.nama_penandatangan) {
                    // Fallback to name match
                    const match = pejabatList.value.find(p => p.nama == formData.nama_penandatangan);
                    if (match) selectedPejabatId.value = match.id;
                }
            } else {
                // NEW DRAFT: Attempt to set Default Pejabat (Active & Default=1)
                const defaultPejabat = pejabatList.value.find(p => p.is_default == 1);
                if (defaultPejabat) {
                    // Force update
                    setPejabat(defaultPejabat.id);
                    selectedPejabatId.value = defaultPejabat.id;
                }
            }

            // Load Global Settings
            if (DRAFT_SETTINGS) {
                Object.assign(globalSettings, DRAFT_SETTINGS);
            } else {
                const savedSettings = localStorage.getItem('sk_editor_settings');
                if (savedSettings) {
                    try {
                        const parsed = JSON.parse(savedSettings);
                        Object.assign(globalSettings, parsed);

                        // Apply Default SK Logo if new draft (no existing SK Logo)
                        if (!DRAFT_DATA && parsed.defaultSkLogo && !formData.skContentLogo) {
                            formData.skContentLogo = parsed.defaultSkLogo;
                            if (!formData.skContentLogoWidth) formData.skContentLogoWidth = 100;
                        }
                    } catch (e) { console.error(e); }
                }
            }
        });

        // Watch Global Settings for Persistence
        watch(globalSettings, (newSettings) => {
            try {
                localStorage.setItem('sk_editor_settings', JSON.stringify(newSettings));
            } catch (e) {
                console.warn('LocalStorage access failed:', e);
            }
        }, { deep: true });

        // --- Watchers for Smart Logic ---

        // 1. Signatory Logic
        const onPejabatSelect = (event) => {
            setPejabat(event.target.value);
        };

        // 2. Date Logic (Indo + Hijri)
        watch(() => formData.tanggal_sk, (newVal) => {
            if (newVal) {
                const date = new Date(newVal);

                // Indo Date
                const indoFormatter = new Intl.DateTimeFormat('id-ID', {
                    day: 'numeric', month: 'long', year: 'numeric'
                });
                formData.tanggal_indo = indoFormatter.format(date);

                // Hijri Date
                const hijriFormatter = new Intl.DateTimeFormat('id-ID-u-ca-islamic', {
                    day: 'numeric', month: 'long', year: 'numeric'
                });
                // Remove "AH" suffix if present and cleanup
                let hijri = hijriFormatter.format(date);
                hijri = hijri.replace(' AH', ' H');
                formData.tanggal_hijri = hijri;
            } else {
                formData.tanggal_indo = '';
                formData.tanggal_hijri = '';
            }
        }, { immediate: true });

        // --- Computed Properties ---
        const previewHtml = computed(() => {
            let html = templateHtml.value;

            // 0. PRE-PROCESS: Inject Logo Width & Custom Logo Override (Kop Logo)
            // Use formData.skLogo if exists (Local Draft), else globalSettings.kopLogo (Default)
            const activeLogo = formData.skLogo || globalSettings.kopLogo;
            const activeWidth = formData.skLogoWidth || globalSettings.kopLogoWidth;

            if (activeLogo) {
                const widthStyle = activeWidth ? `width: ${activeWidth}px` : '';
                const logoPlaceholder = activeLogo; // Use the actual base64/url

                // Regex to find an img tag containing the global placeholder
                const imgPlaceholderRegex = /<img([^>]*?)src=["']\{\{globalSettings\.kopLogo\}\}["']([^>]*?)>/gi;

                html = html.replace(imgPlaceholderRegex, (match, pre, post) => {
                    // Inject style and replace src
                    return `<img${pre}src="${logoPlaceholder}" style="${widthStyle}"${post}>`;
                });
            }

            // 0b. INJECT SK CONTENT LOGO (Garuda/etc) - Top Center of Body (DOM Based)
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
                    img.style.width = `${width}px`;
                    img.style.height = 'auto';
                    img.style.display = 'inline-block';

                    logoDiv.appendChild(img);

                    // FORCE TOP: Insert as the very first element of the body
                    if (doc.body.firstChild) {
                        doc.body.insertBefore(logoDiv, doc.body.firstChild);
                    } else {
                        doc.body.appendChild(logoDiv);
                    }

                    html = doc.body.innerHTML;
                } catch (e) {
                    console.error("Auto-inject logo failed:", e);
                    const logoHtml = `<div style="text-align: center; width: 100%; margin: 0 0 15px 0;"><img src="${formData.skContentLogo}" style="width: ${formData.skContentLogoWidth || 100}px;"></div>`;
                    html = logoHtml + html;
                }
            }

            // 1. Simple Replacements (FormData)
            for (const [key, value] of Object.entries(formData)) {
                if (Array.isArray(value)) continue;
                const regex = new RegExp(`{{${key}}}`, 'g');
                const formattedValue = String(value).replace(/\n/g, '<br>');
                html = html.replace(regex, formattedValue);
            }

            // 1b. Global Settings Replacements
            for (const [key, value] of Object.entries(globalSettings)) {
                const regex = new RegExp(`{{globalSettings.${key}}}`, 'g');
                html = html.replace(regex, value);
            }

            // 2. Repeater Logic
            config.value.forEach(section => {
                section.fields.forEach(field => {
                    if (field.type === 'repeater') {
                        const items = formData[field.variable] || [];
                        const loopRegex = new RegExp(`{{#each ${field.variable}}}([\\s\\S]*?){{/each}}`, 'g');

                        html = html.replace(loopRegex, (match, content) => {
                            return items.map(item => {
                                const formattedItem = String(item).replace(/\n/g, '<br>');
                                return content.replace(/{{this}}/g, formattedItem);
                            }).join('');
                        });
                    }
                });
            });

            // 3. Conditional Logic
            const ifRegex = /{{#if\s+(.*?)}}([\s\S]*?){{\/if}}/g;
            html = html.replace(ifRegex, (match, variable, content) => {
                if (formData[variable]) return content;
                if (globalSettings[variable]) return content;
                return '';
            });

            // 4. INJECT ATTACHMENTS (LAMPIRAN)
            if (formData.attachments && formData.attachments.length > 0) {
                formData.attachments.forEach((att, index) => {
                    const noSK = formData.no_sk || '...';
                    const tanggalIndo = formData.tanggal_indo || '...';
                    const pejabatJabatan = (formData.jabatan_penandatangan || 'PEJABAT').toUpperCase();

                    const lampiranHtml = `
                        <div class="smart-attachment-break" data-title="${att.title || 'Lampiran'}"></div>
                        <div class="attachment-header" style="float: right; text-align: left; width: 50%; margin-bottom: 20px; font-size: ${globalSettings.fontSize || '12pt'}; line-height: 1.5;">
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">LAMPIRAN</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td>KEPUTUSAN ${pejabatJabatan}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">NOMOR</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td>${noSK}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">TANGGAL</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td>${tanggalIndo}</td>
                                </tr>
                            </table>
                        </div>
                        <div style="clear: both;"></div>
                        <div class="attachment-content">
                            ${(att.content || '')}
                        </div>
                    `;
                    html += lampiranHtml;
                });
            }

            return html;
        });

        const paperStyle = computed(() => {
            const width = globalSettings.orientation === 'landscape' ? '297mm' : '210mm';
            const minHeight = globalSettings.orientation === 'landscape' ? '210mm' : '297mm';
            return { width, minHeight, padding: '0' };
        });

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

            // Initialize TinyMCE for the new item
            const index = formData.attachments.length - 1;
            const id = `attachment-editor-${index}`;
            Vue.nextTick(() => {
                initTinyMCE(id, index);
            });
        };

        const removeAttachment = (index) => {
            // Destroy TinyMCE instance first
            const id = `attachment-editor-${index}`;
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

            // Allow time for DOM to render
            setTimeout(() => {
                const isDark = document.documentElement.classList.contains('dark');

                tinymce.init({
                    selector: `#${id}`,
                    menubar: false,
                    statusbar: false,
                    height: 500,
                    skin: isDark ? 'oxide-dark' : 'oxide',
                    content_css: isDark ? 'dark' : 'default',
                    plugins: 'table lists advlist',
                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | table',
                    content_style: 'body { font-family:Times New Roman,Times,serif; font-size:12pt; } table { width: 100% !important; border-collapse: collapse; } td, th { border: 1px solid #000; padding: 4px; vertical-align: top; }',
                    setup: (editor) => {
                        // Set initial value
                        editor.on('init', () => {
                            if (formData.attachments[index] && formData.attachments[index].content) {
                                editor.setContent(formData.attachments[index].content);
                            }
                        });

                        // Sync data on change
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

                    // Try WebP first for best compression with transparency support
                    let dataUrl = canvas.toDataURL('image/webp', quality);

                    // Fallback or Check Size. If browser defaults to PNG (no WebP support), length might be large.
                    // If > 200KB, try reducing dimensions or quality further? 
                    // Note: toDataURL('image/png') ignores quality argument.

                    if (dataUrl.length > 500000) { // If still > 500KB (rare for 300px)
                        // Try JPEG if transparency not critical? No, keep transparency.
                        // Force smaller scale
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

        const handleLogoUpload = (event) => {
            const file = event.target.files[0];
            if (file) {
                // Compress to 300x300, 0.7 quality
                compressImage(file, 300, 300, 0.7, (dataUrl) => {
                    formData.skLogo = dataUrl;
                    if (!formData.skLogoWidth) formData.skLogoWidth = globalSettings.kopLogoWidth || 100;

                    // Reset input
                    event.target.value = '';
                });
            }
        };

        const handleContentLogoUpload = (event) => {
            const file = event.target.files[0];
            if (file) {
                // Compress to 300x300, 0.7 quality
                compressImage(file, 300, 300, 0.7, (dataUrl) => {
                    formData.skContentLogo = dataUrl;
                    if (!formData.skContentLogoWidth) formData.skContentLogoWidth = 100;

                    // Reset input
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

                    // Force WebP
                    const newDataUrl = canvas.toDataURL('image/webp', quality);
                    resolve(newDataUrl);
                };
                img.onerror = () => resolve(base64); // Return original on error
                img.src = base64;
            });
        };

        const sanitizeGlobalSettings = async () => {
            // 1. Check Kop Logo (Threshold: 500KB)
            if (globalSettings.kopLogo && globalSettings.kopLogo.length > 500000) {
                // It's too big, recompress!
                toastr.info("Optimizing Global Logo...", { timeOut: 2000 });
                globalSettings.kopLogo = await recompressBase64(globalSettings.kopLogo, 800, 400, 0.7);

                // Update LocalStorage too to fix it for future
                localStorage.setItem('sk_editor_settings', JSON.stringify(globalSettings));
            }

            // 2. Check Default Content Logo
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
                // Sanitize Global Settings First (Fix large LocalStorage images)
                await sanitizeGlobalSettings();

                // Check payload size client-side first
                const payloadSize = new Blob([JSON.stringify(formData) + JSON.stringify(globalSettings)]).size;
                if (payloadSize > 950000) {
                    const sizeMB = (payloadSize / 1024 / 1024).toFixed(2);
                    toastr.error(`Data too large (${sizeMB}MB). Limit is ~0.95MB. Please compress images or ask admin to increase 'max_allowed_packet'.`);
                    isSaving.value = false;
                    return;
                }

                const response = await fetch(`${siteUrl.value}sk_editor/save`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        data: JSON.stringify(formData),
                        settings: JSON.stringify(globalSettings),
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
                    toastr.success(`Draft Saved Successfully!`);
                    archiveId.value = res.id;
                    const printBtn = document.getElementById('btn-print-hidden');
                    if (printBtn) printBtn.dataset.id = res.id;
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

            // Use explicit dimensions for ALL sizes to force browser compliance
            // A4: 210mm x 297mm
            // F4: 215mm x 330mm
            let width, height;

            if (size === 'A4') {
                width = '210mm'; height = '297mm';
            } else if (size === 'F4') {
                width = '215mm'; height = '330mm';
            } else {
                width = '210mm'; height = '297mm'; // Default A4
            }

            if (orientation === 'landscape') {
                return `${height} ${width}`; // Swap for landscape
            }
            return `${width} ${height}`;
        };

        const printPdf = () => {
            const sizeCSS = getPageSizeCSS();

            // Create a dynamic style element
            const style = document.createElement('style');
            style.id = 'dynamic-print-style';
            // IMPORTANT: @page must be at the top level, NOT inside @media print
            // We set margin to 0 here because we are handling margins inside .paper-page padding
            // We ONLY inject page size here. Visibility is handled by static CSS in editor_view.php
            style.innerHTML = `
                @page {
                    size: ${sizeCSS};
                    margin: 0; 
                }
            `;
            document.head.appendChild(style);

            // Print
            window.print();

            // Cleanup after print dialog closes (or immediately, as styles persist until removed)
            // We use a small timeout to ensure the print dialog has picked up the styles
            setTimeout(() => {
                const el = document.getElementById('dynamic-print-style');
                if (el) el.remove();
            }, 1000);
        };

        const exportWord = () => {
            // Use raw-content to get the full document
            const content = document.getElementById('raw-content').innerHTML;
            const sizeCSS = getPageSizeCSS();

            const preHtml = `<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head><meta charset='utf-8'><title>Export Word</title>
            <style>
                /* Reset */
                body { font-family: 'Times New Roman', serif; font-size: 12pt; margin: 0; padding: 0; }
                
                /* Layout & Typography */
                table { border-collapse: collapse; width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
                td { vertical-align: top; padding: 0; }
                p { margin: 0; padding: 0; }
                
                /* List Styles Fixes */
                ul, ol { margin: 0 0 0.5em 0; padding-left: 2em; list-style-position: outside; }
                li { display: list-item; margin-bottom: 0.25em; }
                ul { list-style-type: disc; }
                ol { list-style-type: decimal; }
                ol[type="a"], ol.lower-alpha { list-style-type: lower-alpha; }
                ol[type="A"], ol.upper-alpha { list-style-type: upper-alpha; }
                ol[type="i"], ol.lower-roman { list-style-type: lower-roman; }
                ol[type="I"], ol.upper-roman { list-style-type: upper-roman; }

                /* Tailwind Mappings */
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-justify { text-align: justify; }
                .font-bold { font-weight: bold; }
                .uppercase { text-transform: uppercase; }
                .underline { text-decoration: underline; }
                .italic { font-style: italic; }
                .text-xs { font-size: 10pt; }
                .text-sm { font-size: 11pt; }
                .text-lg { font-size: 14pt; }
                .text-xl { font-size: 16pt; }
                
                .w-full { width: 100%; }
                .w-1\\/2 { width: 50%; }
                .w-24 { width: 96px; }
                .w-32 { width: 128px; }
                .w-20 { width: 80px; }
                .w-4 { width: 16px; }
                
                .mb-4 { margin-bottom: 12pt; }
                .mb-6 { margin-bottom: 18pt; }
                .mb-8 { margin-bottom: 24pt; }
                .pb-2 { padding-bottom: 6pt; }
                .pl-4 { padding-left: 12pt; }
                .mx-auto { margin-left: auto; margin-right: auto; }
                
                .border-b-4 { border-bottom: 4px solid black; }
                .border-double { border-style: double; }
                .border-black { border-color: black; }
                
                .align-middle { vertical-align: middle; }
                .align-top { vertical-align: top; }
                
                /* Page Setup */
                @page Section1 {
                    size: ${sizeCSS};
                    mso-page-orientation: ${globalSettings.orientation};
                    margin: ${globalSettings.marginTop}mm ${globalSettings.marginRight}mm ${globalSettings.marginBottom}mm ${globalSettings.marginLeft}mm;
                }
                div.Section1 { page: Section1; }
            </style>
            </head><body><div class="Section1">`;
            const postHtml = "</div></body></html>";

            // Note: content contains <div class="paper-preview">...</div> so styles inside .paper-preview might need adjustment
            // But our styles above target elements directly. 
            // We might want to strip the wrapper or just let it be.
            // Let's rely on global tag selectors above.

            const html = preHtml + content + postHtml;

            const blob = new Blob(['\ufeff', html], {
                type: 'application/msword'
            });

            const url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);

            const link = document.createElement('a');
            link.href = url;
            link.download = `SK_${formData.no_sk ? formData.no_sk.replace(/[^a-z0-9]/gi, '_') : 'Draft'}.doc`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        const exportPdf = () => {
            // User requested PDF to match browser print exactly.
            // The best way is to trigger browser print and ask them to "Save as PDF".
            alert("To ensure the PDF matches the editor EXACTLY, please select 'Save as PDF' in the destination dropdown.");

            const sizeCSS = getPageSizeCSS();
            setTimeout(() => {
                const el = document.getElementById('dynamic-print-style-pdf');
                if (el) el.remove();
            }, 1000);
        };

        const fixAutoFormatting = () => {
            const container = document.getElementById('raw-content');
            if (!container) return;

            // Heuristic: Find "Menimbang" label and force list style
            const tds = container.querySelectorAll('td');
            tds.forEach(td => {
                const text = td.textContent.trim();
                // Check if this TD contains "Menimbang" (case-insensitive)
                if (/^menimbang/i.test(text)) {
                    // Look in next siblings for a list
                    let sibling = td.nextElementSibling;
                    while (sibling) {
                        const list = sibling.querySelector('ul, ol');
                        if (list) {
                            // Enforce lower-alpha for Menimbang
                            list.style.listStyleType = 'lower-alpha';
                            if (list.tagName === 'OL') list.setAttribute('type', 'a');

                            // Also ensure <li> items display correctly
                            const items = list.querySelectorAll('li');
                            items.forEach(li => li.style.display = 'list-item');
                        }
                        sibling = sibling.nextElementSibling;
                    }
                }
                // Check for "Mengingat" - usually 1, 2, 3 (decimal)
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

            // Clear previous pages
            outputContainer.innerHTML = '';

            // Get Page Dimensions from Settings
            const mmToPx = 3.78; // Approx 96 DPI
            let pageHeightMm = 297; // Default A4
            let pageWidthMm = 210;

            if (globalSettings.paperSize === 'F4') {
                pageHeightMm = 330; pageWidthMm = 215;
            }
            if (globalSettings.orientation === 'landscape') {
                [pageHeightMm, pageWidthMm] = [pageWidthMm, pageHeightMm];
            }

            const pageHeightPx = pageHeightMm * mmToPx;
            // Margins
            const marginTopPx = (globalSettings.marginTop || 20) * mmToPx;
            const marginBottomPx = (globalSettings.marginBottom || 20) * mmToPx;
            const contentHeightPx = pageHeightPx - marginTopPx - marginBottomPx;

            let pageCount = 0;
            let currentContent = null;
            let currentH = 0;

            const createPage = () => {
                pageCount++;
                const page = document.createElement('div');
                page.className = 'paper-page bg-white shadow-lg relative';
                page.style.width = `${pageWidthMm}mm`;
                page.style.height = `${pageHeightMm}mm`;
                page.style.padding = `${globalSettings.marginTop}mm ${globalSettings.marginRight}mm ${globalSettings.marginBottom}mm ${globalSettings.marginLeft}mm`;

                // TYPOGRAPHY
                page.style.fontSize = globalSettings.fontSize || '12pt';
                page.style.lineHeight = globalSettings.lineHeight || '1.5';

                page.dataset.pageNum = pageCount;

                const content = document.createElement('div');
                content.className = 'page-content';
                content.style.width = '100%';
                content.style.height = '100%';
                content.style.overflow = 'hidden';

                page.appendChild(content);
                outputContainer.appendChild(page);

                currentContent = content;
                currentH = 0;
                return content;
            };

            // Start First Page
            createPage();

            const hasOverflow = (container) => {
                return container.scrollHeight > container.clientHeight + 1;
            };

            // Recursive function to process nodes
            const processNode = (node) => {
                if (node.nodeType === Node.TEXT_NODE && !node.textContent.trim()) return;

                // 0. HANDLE ATTACHMENT BREAKS
                if (node.nodeType === Node.ELEMENT_NODE && node.classList.contains('smart-attachment-break')) {
                    createPage();
                    return; // The break itself is invisible, just triggers a new page
                }

                // 1. Try appending directly
                currentContent.appendChild(node);

                // 2. Check Overflow
                if (hasOverflow(currentContent)) {
                    // Overflow detected!

                    // Remove node
                    currentContent.removeChild(node);

                    // Strategy: Split if Table/List
                    const tag = node.tagName;

                    if (tag === 'TABLE') {
                        // Split Table
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
                            createPage(); // New Page

                            const tablePart2 = node.cloneNode(false);
                            const tbodyPart2 = document.createElement('tbody');
                            tablePart2.appendChild(tbodyPart2);
                            remainingTrs.forEach(tr => tbodyPart2.appendChild(tr));
                            processNode(tablePart2);
                        }

                    } else if (tag === 'UL' || tag === 'OL') {
                        // Split List
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
                        // Atomic Block -> Move to Next Page
                        createPage();

                        currentContent.appendChild(node);
                        // If it still overflows, it's too big for one page. We leave it clipped.
                    }
                }
            };

            // Clone raw content nodes
            const sourceNodes = Array.from(rawContainer.children[0].cloneNode(true).childNodes);
            sourceNodes.forEach(node => processNode(node));

            // 3. POST-PROCESS: PAGE NUMBERING
            if (globalSettings.showPageNumbers) {
                const pages = outputContainer.querySelectorAll('.paper-page');
                const totalPages = pages.length;
                pages.forEach((page, index) => {
                    const pageNum = index + 1;
                    const footer = document.createElement('div');
                    footer.className = 'page-footer absolute text-xs text-gray-500';
                    footer.style.bottom = `${globalSettings.marginBottom / 2}mm`; // Position in the margin area
                    footer.style.right = `${globalSettings.marginRight}mm`;
                    footer.style.fontFamily = 'Arial, sans-serif'; // Footer usually standard font
                    footer.innerText = `Halaman ${pageNum} dari ${totalPages}`;
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
        // Initial Pagination
        onMounted(() => {
            // Wait for DOM & Styles to fully settle (especially images/fonts)
            setTimeout(() => {
                fixAutoFormatting();
                paginate();

                // Initialize TinyMCE for existing attachments
                if (formData.attachments && formData.attachments.length > 0) {
                    formData.attachments.forEach((_, index) => {
                        const id = `attachment-editor-${index}`;
                        initTinyMCE(id, index);
                    });
                }
            }, 500); // 500ms delay for robustness
        });

        return {
            config,
            formData,
            globalSettings,
            isDarkMode,
            toggleTheme,
            previewHtml,
            paperStyle,
            addRepeaterItem,
            removeRepeaterItem,
            addAttachment,
            printPdf,
            exportWord,
            exportPdf,
            fixAutoFormatting,
            pejabatList,
            onPejabatSelect,
            paginate,
            selectedPejabatId
        };
    }
}).mount('#app');
