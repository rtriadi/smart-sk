<div class="space-y-6">
    <?php
    // Pre-calculate stats
    $total_archives = count($archives);
    $total_templates = count($templates);
    
    $current_month = date('Y-m');
    $this_month_count = 0;
    $pending_count = 0;
    
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
        
        // Count pending (Drafts)
        if (isset($arc->no_surat) && stripos($arc->no_surat, 'DRAFT') !== false) {
            $pending_count++;
        }
    }
    ?>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Card 1: Total Archives -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">Total Archives</div>
            <div class="text-3xl font-bold text-slate-800"><?= $total_archives ?></div>
        </div>
        <!-- Card 2: Total Templates -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">Total Templates</div>
            <div class="text-3xl font-bold text-slate-800"><?= $total_templates ?></div>
        </div>
        <!-- Card 3: This Month -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">This Month</div>
            <div class="text-3xl font-bold text-slate-800"><?= $this_month_count ?></div>
        </div>
        <!-- Card 4: Pending -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">Pending</div>
            <div class="text-3xl font-bold text-slate-800"><?= $pending_count ?></div>
        </div>
    </div>

    <!-- Recent Archives Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Recent Documents</h3>
            <a href="<?= site_url('sk_editor/archives') ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wide">View All</a>
        </div>
        
        <?php if (empty($archives)): ?>
            <!-- Empty State -->
            <div class="py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                    <i class="fas fa-file-signature text-slate-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-900">No documents created yet</h3>
                <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto">Create your first Surat Keputusan by selecting a template from the templates menu.</p>
                <div class="mt-6">
                    <a href="<?= site_url('templates') ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> Create New SK
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No. Surat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Template</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php 
                        // Sort by ID desc (newest first)
                        $recent_archives = $archives;
                        usort($recent_archives, function($a, $b) {
                            return $b->id - $a->id;
                        });
                        $recent_archives = array_slice($recent_archives, 0, 5);
                        
                        foreach ($recent_archives as $archive): 
                            $template_name = isset($template_map[$archive->template_id]) ? $template_map[$archive->template_id] : 'Unknown Template';
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    <?= isset($archive->no_surat) ? $archive->no_surat : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <?= $template_name ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <?= isset($archive->created_at) ? date('d M Y', strtotime($archive->created_at)) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= site_url('sk_editor/edit_draft/' . $archive->id) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= site_url('sk_editor/generate_pdf/' . $archive->id) ?>" target="_blank" class="text-slate-600 hover:text-red-600 mr-3" title="PDF">
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
