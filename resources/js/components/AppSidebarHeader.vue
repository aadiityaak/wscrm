<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Menu } from 'lucide-vue-next';
import { inject, ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    useCustomSidebar?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    useCustomSidebar: false,
});

// Check if SidebarContext is available
const sidebarContext = inject('SidebarContext', null);
const hasRekaUISidebar = sidebarContext !== null;

// For custom sidebar (AppSidebar), we need to handle toggle manually
const emit = defineEmits<{
    toggleSidebar: [];
}>();
</script>

<template>
    <header class="sticky top-0 z-40 flex h-16 items-center gap-4 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 px-6">
        <!-- Reka UI Sidebar Trigger (for CustomerLayout) -->
        <SidebarTrigger v-if="hasRekaUISidebar" />

        <!-- Custom Sidebar Trigger (for AppSidebarLayout) -->
        <Button
            v-else
            variant="ghost"
            size="icon"
            @click="emit('toggleSidebar')"
            class="h-9 w-9"
        >
            <Menu class="h-4 w-4" />
        </Button>

        <template v-if="breadcrumbs && breadcrumbs.length > 0">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </template>
    </header>
</template>
