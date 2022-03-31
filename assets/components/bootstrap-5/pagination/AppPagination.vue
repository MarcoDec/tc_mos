<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        count: {required: true, type: Number},
        current: {required: true, type: Number},
        perPage: {default: 15, type: Number}
    })
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
