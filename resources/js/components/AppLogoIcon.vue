<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 40" :class="className" v-bind="$attrs">
        <!-- W -->
        <path
            fill="currentColor"
            d="M2 4 L8 32 L14 4 L18 4 L24 32 L30 4 L34 4 L26 38 L22 38 L16 10 L10 38 L6 38 Z"
        />
        <!-- s -->
        <path
            fill="currentColor"
            d="M42 16 C40 14.5 38 14 36 14 C34.5 14 33.5 14.5 33.5 15.5 C33.5 16.5 34.5 17 37 17.5 C40 18 42 19 42 21.5 C42 24 40 26 36.5 26 C34 26 32 25 30.5 23.5 L32.5 21 C33.5 22 35 22.5 36.5 22.5 C38 22.5 38.5 22 38.5 21.5 C38.5 20.5 37.5 20 35.5 19.5 C32.5 19 30.5 18 30.5 15.5 C30.5 13 32.5 11 36 11 C38.5 11 40.5 12 42 13.5 Z"
        />
        <!-- Dot -->
        <circle
            fill="currentColor"
            cx="48" cy="32" r="3"
        />
    </svg>
</template>
