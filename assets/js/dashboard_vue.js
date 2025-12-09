const { createApp, ref, computed } = Vue;

createApp({
    setup() {
        // State
        const templates = ref(TEMPLATES_DATA);
        const searchQuery = ref('');
        const siteUrl = ref(SITE_URL);

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
        const createUrl = (id) => {
            return `${siteUrl.value}sk_editor/create/${id}`;
        };

        const manageUrl = () => {
            return `${siteUrl.value}sk_editor/settings`;
        };

        return {
            templates,
            searchQuery,
            filteredTemplates,
            createUrl,
            manageUrl,
            isDarkMode,
            toggleTheme
        };
    }
}).mount('#app');
