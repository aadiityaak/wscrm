<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import { useSidebar } from '@/composables/useSidebar';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { isMinimized } = useSidebar();
</script>

<template>
    <div class="flex min-h-screen">
        <AppSidebar />
        <div 
            :class="[
                'flex-1 flex flex-col transition-all duration-300',
                isMinimized ? 'ml-16' : 'ml-64'
            ]"
        >
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
