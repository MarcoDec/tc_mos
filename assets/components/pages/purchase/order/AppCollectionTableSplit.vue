<script setup>
    import AppTableSplit from '../../../../components/app-collection-split-new/AppTableSplit.vue'
    import {defineProps} from 'vue'

    defineProps({
        item: {required: true, type: Object}
    })
    const fields = [
        {
            label: 'Quantité confirmée',
            name: 'quantite',
            sort: false,
            type: 'text',
            update: true
        },
        {
            label: 'Date confirmée',
            name: 'date',
            sort: false,
            type: 'date',
            update: true
        }
    ]
</script>

<template>
    <div>
        <AppRow>
            <AppCol>
                <AppCard>
                    <span>Quantité souhaitée: {{ item.quantite }}</span>
                    <span>Date souhaitée: {{ item.date }}</span>
                </AppCard>
            </AppCol>
        </AppRow>
        <div>
            <AppTableSplit :fields="fields" :item="item"/>
        </div>
    </div>
</template>
