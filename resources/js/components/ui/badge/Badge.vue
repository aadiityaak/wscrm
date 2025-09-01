<script setup lang="ts">
import { computed } from 'vue';
import { cva, type VariantProps } from 'class-variance-authority';

const badgeVariants = cva(
  'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
  {
    variants: {
      variant: {
        default: 'bg-primary text-primary-foreground',
        secondary: 'bg-secondary text-secondary-foreground',
        destructive: 'bg-destructive text-destructive-foreground',
        outline: 'text-foreground border border-input bg-background',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  }
);

interface Props {
  variant?: VariantProps<typeof badgeVariants>['variant'];
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
});

const badgeClass = computed(() => 
  badgeVariants({ variant: props.variant, class: props.class })
);
</script>

<template>
  <div :class="badgeClass">
    <slot />
  </div>
</template>