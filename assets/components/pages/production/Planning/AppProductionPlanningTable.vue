<script setup>
    import {computed, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import {useProductionPlanningsFieldsStore} from '../../../../stores/productionPlannings/productionPlannings'

    const props = defineProps({
        fields: {required: true, type: Array}
    })
    const route = useRoute()
    const isLoaded = ref(false)
    const storeProductionPlanningsFields = useProductionPlanningsFieldsStore()
    //storeProductionPlanningsFields.fetch()
    storeProductionPlanningsFields.fetch().then(() => {
        isLoaded.value = true
    })

    const combinedFields = computed(() => [...props.fields, ...storeProductionPlanningsFields.fields])

    const currentPage = ref(1)
    const itemsPerPage = 10

    const paginatedItems = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage
        return storeProductionPlanningsFields.items.slice(start, start + itemsPerPage)
    })

    const totalPages = computed(() => Math.ceil(storeProductionPlanningsFields.items.length / itemsPerPage))

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
            currentPage.value++
        }
    }

    const prevPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--
        }
    }
</script>

<template>
    <div v-if="isLoaded" class="tableFixHead">
        <table :id="route.name" class="schedule-table">
            <thead>
            <tr>
                <th v-for="field in combinedFields" :key="field.name" :style="{width: field.width}">
                    {{ field.label }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in paginatedItems" :key="item.id">
                <td v-for="field in combinedFields" :key="field.name">
                    {{ item[field.name] }}
                </td>
            </tr>
            </tbody>
        </table>
        <div class="pagination">
            <button :disabled="currentPage === 1" @click="prevPage">
                Previous
            </button>
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <button :disabled="currentPage === totalPages" @click="nextPage">
                Next
            </button>
        </div>
    </div>
    <div v-else class="text-center">
        Loading... Please wait...<br>
        <span :class="text" class="spinner-border" role="status"/>
    </div>
</template>

<style scoped>
    .schedule-table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
    }
    .schedule-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .schedule-table th,
    .schedule-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .pagination {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    .pagination button {
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 4px;
    }

    .pagination button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
