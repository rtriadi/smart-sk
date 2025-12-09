const { createApp, ref, computed } = Vue;

createApp({
    setup() {
        // State
        const templates = ref(TEMPLATES_DATA);
        const siteUrl = ref(SITE_URL);
        const searchQuery = ref('');

        // Computed
        const filteredTemplates = computed(() => {
            if (!searchQuery.value) return templates.value;
            const lowerQuery = searchQuery.value.toLowerCase();
            return templates.value.filter(t =>
                t.nama_sk.toLowerCase().includes(lowerQuery) ||
                t.kategori.toLowerCase().includes(lowerQuery)
            );
        });

        // Theme Logic
        const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');

        Vue.onMounted(() => {
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
        const createUrl = () => `${siteUrl.value}templates/create`;
        const editUrl = (id) => `${siteUrl.value}templates/edit/${id}`;
        const deleteUrl = (id) => `${siteUrl.value}templates/delete/${id}`;
        const useUrl = (id) => `${siteUrl.value}sk_editor/create/${id}`;
        const dashboardUrl = () => `${siteUrl.value}sk_editor`; // Point to main dashboard
        const settingsUrl = () => `${siteUrl.value}settings`;

        const confirmDelete = (event) => {
            if (!confirm('Are you sure you want to delete this template?')) {
                event.preventDefault();
            }
        };

        return {
            templates,
            searchQuery,
            filteredTemplates,
            createUrl,
            editUrl,
            deleteUrl,
            useUrl,
            dashboardUrl,
            settingsUrl,
            confirmDelete,
            isDarkMode,
            toggleTheme
        };
    }
}).mount('#app');
