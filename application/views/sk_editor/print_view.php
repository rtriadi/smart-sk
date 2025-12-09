<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview - Smart SK Editor</title>
    <!-- Tailwind CSS for Interface -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Toolbar styles */
        .preview-toolbar {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            backdrop-filter: blur(5px);
            z-index: 1000;
            display: flex;
            gap: 10px;
            border: 1px solid #e2e8f0;
        }
        @media print {
            .preview-toolbar { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Toolbar -->
    <div class="h-16 bg-white border-b flex items-center justify-between px-6 shadow-sm z-50 relative">
        <div class="flex items-center">
             <a href="<?php echo site_url('sk_editor/archives'); ?>" class="text-gray-500 hover:text-indigo-600 transition mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-lg font-bold text-gray-800">Print Preview</h1>
        </div>
        <div class="flex space-x-3">
             <button onclick="printFrame()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow-md font-semibold transition flex items-center">
                <i class="fas fa-print mr-2"></i> Print Document
            </button>
            <a href="<?php echo site_url('sk_editor/generate_pdf/'.$archive_id); ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow-md font-semibold transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Iframe Container -->
    <div class="w-full h-[calc(100vh-64px)] bg-gray-600 flex justify-center overflow-hidden">
        <iframe 
            id="printFrame" 
            src="<?php echo site_url('sk_editor/preview_content/' . $archive_id); ?>" 
            class="w-full h-full border-0" 
            title="Document Preview">
        </iframe>
    </div>

    <script>
        // Print function
        function printFrame() {
            const iframe = document.getElementById('printFrame');
            const frameWindow = iframe.contentWindow;
            frameWindow.focus();
            frameWindow.print();
        }
    </script>

</body>
</html>
