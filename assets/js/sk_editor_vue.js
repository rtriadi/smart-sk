const { createApp, ref, reactive, computed, onMounted, watch } = Vue;

createApp({
    setup() {
        // --- State ---
        const config = ref(TEMPLATE_CONFIG);
        const templateHtml = ref(TEMPLATE_HTML);
        const templateId = ref(TEMPLATE_ID);
        const siteUrl = ref(SITE_URL);
        const archiveId = ref(ARCHIVE_ID);

        // Form Data (Reactive)
        const formData = reactive({});

        // Global Settings (Reactive & Persistent)
        const globalSettings = reactive({
            paperSize: 'A4', // A4, F4, Legal
            orientation: 'portrait', // portrait, landscape
            marginTop: 20,
            marginBottom: 20,
            marginLeft: 25,
            marginRight: 20,
            showKop: true,
            // Kop Surat Fields
            kopLogo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/Logo_of_the_Ministry_of_Religious_Affairs_of_the_Republic_of_Indonesia.svg/1200px-Logo_of_the_Ministry_of_Religious_Affairs_of_the_Republic_of_Indonesia.svg.png',
            kopTitle1: 'MAHKAMAH AGUNG REPUBLIK INDONESIA',
            kopTitle2: 'DIREKTORAT JENDERAL BADAN PERADILAN AGAMA',
            kopTitle3: 'PENGADILAN TINGGI AGAMA GORONTALO',
            kopTitle4: 'PENGADILAN AGAMA GORONTALO',
            kopAddress: 'Jalan Achmad Nadjamuddin No.22, Dulalowo Timur, Kecamatan Kota Tengah\nKota Gorontalo, 96138. www.pa-gorontalo.go.id, surat@pa-gorontalo.go.id'
        });

        // Theme Logic
        const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');

        // --- Initialization ---
        onMounted(() => {
            // Apply initial theme
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Initialize formData

            // Initialize formData with defaults FIRST
            config.value.forEach(section => {
                section.fields.forEach(field => {
                    if (field.type === 'repeater') {
                        formData[field.variable] = [];
                    } else {
                        formData[field.variable] = field.default || '';
                    }
                });
            });

            // Then overwrite with saved draft data if it exists
            if (DRAFT_DATA) {
                // Only overwrite fields that exist in DRAFT_DATA
                // This preserves new fields that might not be in the draft
                Object.assign(formData, DRAFT_DATA);
            }

            // Load Global Settings
            if (DRAFT_SETTINGS) {
                Object.assign(globalSettings, DRAFT_SETTINGS);
            } else {
                const savedSettings = localStorage.getItem('sk_editor_settings');
                if (savedSettings) {
                    Object.assign(globalSettings, JSON.parse(savedSettings));
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
        watch(() => formData.jabatan_selector, (newVal) => {
            if (!newVal) return;
            const titles = {
                'Ketua': 'KETUA PENGADILAN AGAMA GORONTALO',
                'Wakil Ketua': 'WAKIL KETUA PENGADILAN AGAMA GORONTALO',
                'Panitera': 'PANITERA PENGADILAN AGAMA GORONTALO',
                'Sekretaris': 'SEKRETARIS PENGADILAN AGAMA GORONTALO'
            };
            if (titles[newVal]) {
                formData.jabatan_penandatangan = titles[newVal];
            }
        });

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

            // 1. Simple Replacements (FormData)
            for (const [key, value] of Object.entries(formData)) {
                if (Array.isArray(value)) continue; // Skip repeaters for now
                const regex = new RegExp(`{{${key}}}`, 'g');
                // Convert newlines to <br> for textareas
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
                                // Convert newlines to <br> for repeater items
                                const formattedItem = String(item).replace(/\n/g, '<br>');
                                return content.replace(/{{this}}/g, formattedItem);
                            }).join('');
                        });
                    }
                });
            });

            // 3. Conditional Logic ({{#if variable}})
            const ifRegex = /{{#if\s+(.*?)}}([\s\S]*?){{\/if}}/g;
            html = html.replace(ifRegex, (match, variable, content) => {
                // Check formData
                if (formData[variable]) return content;
                // Check globalSettings
                if (globalSettings[variable]) return content;

                return '';
            });

            return html;
        });

        const paperStyle = computed(() => {
            const width = globalSettings.orientation === 'landscape' ? '297mm' : '210mm'; // Base A4 width/height swap
            const minHeight = globalSettings.orientation === 'landscape' ? '210mm' : '297mm';

            // Adjust for F4/Legal if needed (simplified for now, can expand)

            return {
                width: width,
                minHeight: minHeight,
                padding: '0'
            };
        });

        const addRepeaterItem = (variable) => {
            if (!formData[variable]) formData[variable] = [];
            formData[variable].push(''); // Simply push an empty string or object for now
        };
        const removeRepeaterItem = (variable, index) => {
            if (formData[variable]) {
                formData[variable].splice(index, 1);
            }
        };

        const handleLogoUpload = (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    globalSettings.kopLogo = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };

        const isSaving = ref(false);

        const saveDraft = async () => {
            if (isSaving.value) return;
            isSaving.value = true;
            try {
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
            style.innerHTML = `
                @page {
                    size: ${sizeCSS};
                    margin: 0; 
                }
                @media print {
                    /* Hide everything by default */
                    body * {
                        visibility: hidden;
                    }
                    
                    /* Show only the pagination container and its children */
                    #pagination-container, #pagination-container * {
                        visibility: visible;
                    }
                    
                    /* Position the container at the top left */
                    #pagination-container {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        margin: 0 !important;
                        padding: 0 !important;
                        /* Remove the space-y-8 gap from editor view */
                        display: block !important; 
                    }
                    
                    /* Style individual pages for print */
                    .paper-page {
                        box-shadow: none !important;
                        margin: 0 !important;
                        border: none !important;
                        /* Force page break after each page */
                        break-after: page;
                        page-break-after: always;
                        /* Ensure background is white */
                        background: white !important;
                        /* Reset any transform/scale */
                        transform: none !important;
                    }
                    
                    /* Hide the last page break to avoid an empty trailing page */
                    .paper-page:last-child {
                        break-after: auto;
                        page-break-after: auto;
                    }
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

        // Pejabat Logic
        const pejabatList = ref(typeof PEJABAT_DATA !== 'undefined' ? PEJABAT_DATA : []);
        const onPejabatChange = (event) => {
            const selectedName = event.target.value;
            const pejabat = pejabatList.value.find(p => p.nama === selectedName);
            if (pejabat) {
                if (typeof formData.nip_penandatangan !== 'undefined') {
                    formData.nip_penandatangan = pejabat.nip;
                }
                if (typeof formData.jabatan_penandatangan !== 'undefined') {
                    formData.jabatan_penandatangan = pejabat.jabatan;
                }
            }
        };


        const paginate = () => {
            const rawContainer = document.getElementById('raw-content');
            const outputContainer = document.getElementById('pagination-container');
            if (!rawContainer || !outputContainer) return;

            // Clear previous pages
            outputContainer.innerHTML = '';

            // Get Page Dimensions from Settings
            // We need pixel values for calculations. 
            // Assuming 96 DPI (standard for screen), 1mm = 3.78px
            const mmToPx = 3.78;

            // Parse dimensions from globalSettings or defaults
            let pageHeightMm = 297; // Default A4
            let pageWidthMm = 210;

            if (globalSettings.paperSize === 'F4') {
                pageHeightMm = 330; pageWidthMm = 215;
            }

            if (globalSettings.orientation === 'landscape') {
                [pageHeightMm, pageWidthMm] = [pageWidthMm, pageHeightMm];
            }

            const pageHeightPx = pageHeightMm * mmToPx;
            const pageWidthPx = pageWidthMm * mmToPx;

            const marginTopPx = (globalSettings.marginTop || 20) * mmToPx;
            const marginBottomPx = (globalSettings.marginBottom || 20) * mmToPx;
            const marginLeftPx = (globalSettings.marginLeft || 25) * mmToPx;
            const marginRightPx = (globalSettings.marginRight || 20) * mmToPx;

            const contentHeightPx = pageHeightPx - marginTopPx - marginBottomPx;

            // Helper to create a new page
            const createPage = (pageNum) => {
                const page = document.createElement('div');
                page.className = 'paper-page bg-white shadow-lg relative';
                page.style.width = `${pageWidthMm}mm`;
                page.style.height = `${pageHeightMm}mm`;
                page.style.padding = `${globalSettings.marginTop}mm ${globalSettings.marginRight}mm ${globalSettings.marginBottom}mm ${globalSettings.marginLeft}mm`;
                page.style.boxSizing = 'border-box';
                page.style.overflow = 'hidden'; // Hide overflow
                page.dataset.pageNum = pageNum;

                // Page Content Wrapper
                const content = document.createElement('div');
                content.className = 'page-content';
                content.style.width = '100%';
                content.style.height = '100%';
                page.appendChild(content);

                outputContainer.appendChild(page);
                return content;
            };

            let currentPageContent = createPage(1);
            let currentHeight = 0;

            // Clone raw content to avoid destroying the source
            // IMPORTANT: sourceNodes must be from the DOM *after* fixAutoFormatting
            const sourceNodes = Array.from(rawContainer.children[0].cloneNode(true).childNodes);

            sourceNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE && !node.textContent.trim()) return;

                // Temporarily append to check height
                currentPageContent.appendChild(node);
                const nodeHeight = node.offsetHeight || 0;

                // Check if node fits
                if (currentHeight + nodeHeight > contentHeightPx) {
                    // Overflow!
                    // Remove from current page
                    currentPageContent.removeChild(node);

                    // Create new page
                    currentPageContent = createPage(document.querySelectorAll('.paper-page').length + 1);
                    currentHeight = 0;

                    // Append to new page
                    currentPageContent.appendChild(node);
                    currentHeight += nodeHeight;
                } else {
                    currentHeight += nodeHeight;
                }
            });
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

        // Initial Pagination
        onMounted(() => {
            Vue.nextTick(() => {
                fixAutoFormatting();
                paginate();
            });
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
            handleLogoUpload,
            saveDraft,
            isSaving,
            printPdf,
            exportWord,
            exportPdf,
            fixAutoFormatting,
            pejabatList,
            onPejabatChange,
            paginate
        };
    }
}).mount('#app');
