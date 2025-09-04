<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import type { BreadcrumbItemType } from '@/types';
import { Button } from '@/components/ui/button';
import { Menu } from 'lucide-vue-next';
import { useSidebar } from '@/composables/useSidebar';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { toggleSidebar } = useSidebar();
</script>

<template>
    <header class="flex h-16 items-center gap-4 border-b border-border px-6">
        <Button 
            variant="ghost" 
            size="sm" 
            @click="toggleSidebar"
            class="p-2 hover:bg-sidebar-accent"
        >
            <Menu class="h-4 w-4" />
        </Button>
        
        <template v-if="breadcrumbs && breadcrumbs.length > 0">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </template>
    </header>
</template>
