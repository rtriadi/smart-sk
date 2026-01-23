<div class="space-y-6">
    <?php
    // Pre-calculate stats
    $total_archives = count($archives);
    $total_templates = count($templates);
    
    $current_month = date('Y-m');
    $this_month_count = 0;
    $draft_count = 0;
    $final_count = 0;
    
    // Create template map for name lookup
    $template_map = [];
    foreach ($templates as $t) {
        $template_map[$t->id] = $t->nama_sk;
    }

    foreach ($archives as $arc) {
        // Count this month
        if (isset($arc->created_at) && strpos($arc->created_at, $current_month) === 0) {
            $this_month_count++;
        }
        
        // Count drafts vs final
        if (isset($arc->no_surat) && stripos($arc->no_surat, 'DRAFT') !== false) {
            $draft_count++;
        } else {
            $final_count++;
        }
    }
    ?>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Card 1: Total SK Final -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium mb-1">SK Final</div>
                    <div class="text-3xl font-bold text-green-600"><?= $final_count ?></div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <!-- Card 2: Total Draft -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium mb-1">Draft</div>
                    <div class="text-3xl font-bold text-amber-600"><?= $draft_count ?></div>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
        <!-- Card 3: Total Templates -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium mb-1">Template</div>
                    <div class="text-3xl font-bold text-slate-800"><?= $total_templates ?></div>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-code text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <!-- Card 4: This Month -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium mb-1">Bulan Ini</div>
                    <div class="text-3xl font-bold text-slate-800"><?= $this_month_count ?></div>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-teal-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Drafts Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-alt text-amber-500"></i>
                <h3 class="font-bold text-slate-800">Draft Terbaru</h3>
            </div>
            <a href="<?= site_url('sk_editor/archives') ?>" class="text-xs font-bold text-teal-600 hover:text-teal-800 uppercase tracking-wide">Lihat Semua</a>
        </div>
        
        <?php 
        // Filter drafts only
        $draft_archives = array_filter($archives, function($arc) {
            return isset($arc->no_surat) && stripos($arc->no_surat, 'DRAFT') !== false;
        });
        
        if (empty($draft_archives)): 
        ?>
            <!-- Empty State -->
            <div class="py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                    <i class="fas fa-file-alt text-slate-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-900">Belum ada draft</h3>
                <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto">Buat SK baru dengan memilih template dari menu template.</p>
                <div class="mt-6">
                    <a href="<?= site_url('templates') ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        <i class="fas fa-plus mr-2"></i> Buat SK Baru
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kode Draft</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Template</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php 
                        // Sort by ID desc (newest first)
                        usort($draft_archives, function($a, $b) {
                            return $b->id - $a->id;
                        });
                        $recent_drafts = array_slice($draft_archives, 0, 5);
                        
                        foreach ($recent_drafts as $archive): 
                            $template_name = isset($template_map[$archive->template_id]) ? $template_map[$archive->template_id] : 'Unknown Template';
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <?= isset($archive->no_surat) ? $archive->no_surat : '-' ?>
                                        <span class="bg-amber-100 text-amber-700 text-[10px] px-1.5 py-0.5 rounded font-semibold">DRAFT</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <?= $template_name ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <?= isset($archive->created_at) ? date('d M Y', strtotime($archive->created_at)) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="<?= site_url('sk_editor/edit_draft/' . $archive->id) ?>" class="text-amber-500 hover:text-amber-700" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="<?= site_url('sk_editor/print_draft/' . $archive->id) ?>" target="_blank" class="text-sky-500 hover:text-sky-700" title="Cetak">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Final SK Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500"></i>
                <h3 class="font-bold text-slate-800">SK Final Terbaru</h3>
            </div>
            <a href="<?= site_url('sk_editor/archives') ?>" class="text-xs font-bold text-teal-600 hover:text-teal-800 uppercase tracking-wide">Lihat Semua</a>
        </div>
        
        <?php 
        // Filter final SK only
        $final_archives = array_filter($archives, function($arc) {
            return !isset($arc->no_surat) || stripos($arc->no_surat, 'DRAFT') === false;
        });
        
        if (empty($final_archives)): 
        ?>
            <!-- Empty State -->
            <div class="py-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                    <i class="fas fa-check-circle text-slate-400 text-xl"></i>
                </div>
                <h3 class="text-sm font-medium text-slate-700">Belum ada SK final</h3>
                <p class="mt-1 text-xs text-slate-500">Finalisasi draft untuk memindahkannya ke sini.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Surat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Template</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php 
                        // Sort by ID desc (newest first)
                        $final_archives = array_values($final_archives);
                        usort($final_archives, function($a, $b) {
                            return $b->id - $a->id;
                        });
                        $recent_final = array_slice($final_archives, 0, 5);
                        
                        foreach ($recent_final as $archive): 
                            $template_name = isset($template_map[$archive->template_id]) ? $template_map[$archive->template_id] : 'Unknown Template';
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <?= isset($archive->no_surat) ? $archive->no_surat : '-' ?>
                                        <span class="bg-green-100 text-green-700 text-[10px] px-1.5 py-0.5 rounded font-semibold">FINAL</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <?= $template_name ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <?= isset($archive->finalized_at) ? date('d M Y', strtotime($archive->finalized_at)) : (isset($archive->created_at) ? date('d M Y', strtotime($archive->created_at)) : '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="<?= site_url('sk_editor/edit_draft/' . $archive->id) ?>" class="text-amber-500 hover:text-amber-700" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= site_url('sk_editor/generate_pdf/' . $archive->id) ?>" target="_blank" class="text-red-500 hover:text-red-700" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
