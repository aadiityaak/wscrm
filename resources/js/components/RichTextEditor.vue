<script setup lang="ts">
import Editor from '@tinymce/tinymce-vue';
import { useVModel } from '@vueuse/core';

interface Props {
  modelValue?: string;
  placeholder?: string;
  height?: number;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  placeholder: 'Tulis konten di sini...',
  height: 400,
  disabled: false,
});

const emits = defineEmits<{
  'update:modelValue': [value: string];
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
});

const editorConfig = {
  height: props.height,
  menubar: true,
  plugins: [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
    'insertdatetime', 'media', 'table', 'help', 'wordcount'
  ],
  toolbar: 'undo redo | blocks | ' +
    'bold italic forecolor | alignleft aligncenter ' +
    'alignright alignjustify | bullist numlist outdent indent | ' +
    'removeformat | help',
  content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px }',
  placeholder: props.placeholder,
  branding: false,
  resize: 'vertical',
  paste_as_text: false,
  paste_data_images: true,
  automatic_uploads: false,
  file_picker_types: 'image',
  setup: (editor: any) => {
    editor.on('change', () => {
      modelValue.value = editor.getContent();
    });
  },
};
</script>

<template>
  <div class="rich-text-editor">
    <Editor
      v-model="modelValue"
      :init="editorConfig"
      :disabled="disabled"
      api-key="no-api-key"
    />
  </div>
</template>

<style scoped>
.rich-text-editor {
  @apply w-full;
}

/* TinyMCE dark mode support */
:deep(.tox-tinymce) {
  border: 1px solid hsl(var(--border)) !important;
  border-radius: 6px !important;
}

:deep(.tox-editor-header) {
  border-bottom: 1px solid hsl(var(--border)) !important;
}

/* Dark mode adjustments */
.dark :deep(.tox-tinymce) {
  background: hsl(var(--background)) !important;
}

.dark :deep(.tox-toolbar__primary) {
  background: hsl(var(--background)) !important;
  border-bottom: 1px solid hsl(var(--border)) !important;
}

.dark :deep(.tox-edit-area__iframe) {
  background: hsl(var(--background)) !important;
}
</style>