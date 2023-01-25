<script lang="ts" setup>
  import {computed, defineProps, withDefaults} from 'vue'
  const props = withDefaults(defineProps<{count: number, current: number, perPage?: number}>(), {perPage: 15})
  const pages = computed(() => Math.ceil(props.count / props.perPage))
</script>

<template>
  <nav>
    <ul class="pagination">
      <AppPaginationItem>Début</AppPaginationItem>
      <AppPaginationItem>Préc.</AppPaginationItem>
      <AppPaginationItem v-for="page in pages" :key="page" :current="current === page">
        {{ page }}
      </AppPaginationItem>
      <AppPaginationItem>Suiv.</AppPaginationItem>
      <AppPaginationItem>Fin</AppPaginationItem>
    </ul>
  </nav>
</template>
