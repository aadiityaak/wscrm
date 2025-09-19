import { readonly, ref, watch } from 'vue';

// Get initial state from cookie - default to minimized (icon only)
const getInitialState = (): boolean => {
    if (typeof document === 'undefined') return true; // Default to minimized
    const cookie = document.cookie.split('; ').find((row) => row.startsWith('sidebar-minimized='));
    return cookie ? cookie.split('=')[1] === 'true' : true; // Default to minimized
};

// Save state to cookie
const saveToCookie = (value: boolean): void => {
    if (typeof document === 'undefined') return;
    document.cookie = `sidebar-minimized=${value}; path=/; max-age=${60 * 60 * 24 * 365}`; // 1 year
};

const isMinimized = ref(getInitialState());
const isMobileOpen = ref(false);

// Watch mobile sidebar for body scroll lock
watch(isMobileOpen, (isOpen) => {
    if (typeof document === 'undefined') return;

    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

// Watch for changes and save to cookie
watch(
    isMinimized,
    (newValue) => {
        saveToCookie(newValue);
    },
    { immediate: false },
);

export function useSidebar() {
    const toggleSidebar = () => {
        isMinimized.value = !isMinimized.value;
    };

    const toggleMobileSidebar = () => {
        isMobileOpen.value = !isMobileOpen.value;
    };

    const closeMobileSidebar = () => {
        isMobileOpen.value = false;
    };

    const openMobileSidebar = () => {
        isMobileOpen.value = true;
    };

    const minimize = () => {
        isMinimized.value = true;
    };

    const expand = () => {
        isMinimized.value = false;
    };

    return {
        isMinimized: readonly(isMinimized),
        isMobileOpen: readonly(isMobileOpen),
        toggleSidebar,
        toggleMobileSidebar,
        closeMobileSidebar,
        openMobileSidebar,
        minimize,
        expand,
    };
}
