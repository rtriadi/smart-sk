const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        // State
        const archives = ref(ARCHIVES_DATA);
        const siteUrl = ref(SITE_URL);
        const searchQuery = ref('');

        // Computed
        const filteredArchives = computed(() => {
            if (!searchQuery.value) return archives.value;
            const lowerQuery = searchQuery.value.toLowerCase();
            return archives.value.filter(a =>
                a.no_surat.toLowerCase().includes(lowerQuery) ||
                a.nama_sk.toLowerCase().includes(lowerQuery)
            );
        });

        // Theme Logic
        const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');

        onMounted(() => {
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });

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

        // Methods
        const editUrl = (id) => `${siteUrl.value}sk_editor/edit_draft/${id}`; // Load into editor to edit draft
        const printUrl = (id) => `${siteUrl.value}sk_editor/print_draft/${id}`; // Quick print functionality
        const dashboardUrl = () => `${siteUrl.value}sk_editor`; // Point to main dashboard

        const deleteDraft = (id) => {
            if (confirm('Are you sure you want to delete this draft? This action cannot be undone.')) {
                window.location.href = `${siteUrl.value}sk_editor/delete_draft/${id}`;
            }
        };

        const cloneUrl = (id) => `${siteUrl.value}sk_editor/clone_draft/${id}`;

        const renameDraft = async (item) => {
            const newName = prompt("Rename Draft:", item.no_surat);
            if (newName && newName !== item.no_surat) {
                try {
                    // Use URLSearchParams for x-www-form-urlencoded expected by CI3 usually, or FormData
                    const params = new URLSearchParams();
                    params.append('id', item.id);
                    params.append('name', newName);

                    const response = await fetch(`${siteUrl.value}sk_editor/rename_draft`, {
                        method: 'POST',
                        body: params
                    });
                    const res = await response.json();

                    if (res.status === 'success') {
                        // Update local state directy
                        item.no_surat = newName;
                        toastr.success('Draft renamed successfully');
                    } else {
                        toastr.error('Failed to rename: ' + (res.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error(e);
                    toastr.error('Error renaming draft');
                }
            }
        };

        return {
            archives,
            searchQuery,
            filteredArchives,
            editUrl,
            printUrl,
            deleteDraft,
            cloneUrl,
            renameDraft,
            dashboardUrl,
            isDarkMode,
            toggleTheme
        };
    }
}).mount('#app');
