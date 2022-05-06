<script setup>
    import AppPaginationItem from './AppPaginationItem.vue'

    defineProps({coll: {required: true, type: Object}})
    const emit = defineEmits(['click'])

    function click(page) {
        emit('click', page)
    }
</script>

<template>
    <nav>
        <ul class="pagination">
            <AppPaginationItem :page="coll.first" @click="click">
                Début
            </AppPaginationItem>
            <AppPaginationItem :page="coll.prev" @click="click">
                Préc.
            </AppPaginationItem>
            <AppPaginationItem
                v-for="page in coll.pages"
                :key="page"
                :current="coll.page === page"
                :page="page"
                @click="click">
                {{ page }}
            </AppPaginationItem>
            <AppPaginationItem :page="coll.next" @click="click">
                Suiv.
            </AppPaginationItem>
            <AppPaginationItem :page="coll.last" @click="click">
                Fin
            </AppPaginationItem>
        </ul>
    </nav>
</template>
