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

        return {
            archives,
            searchQuery,
            filteredArchives,
            editUrl,
            printUrl,
            deleteDraft,
            dashboardUrl,
            isDarkMode,
            toggleTheme
        };
    }
}).mount('#app');
