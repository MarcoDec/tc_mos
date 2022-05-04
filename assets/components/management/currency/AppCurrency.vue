<script setup>
    import {useRepo, useRouter} from '../../../composition'
    import {CurrencyRepository} from '../../../store/modules'
    import {computed} from 'vue'

    const props = defineProps({currency: {required: true, type: Object}})
    const {id} = useRouter()
    const field = {name: 'active', type: 'boolean'}
    const inputId = computed(() => `${props.currency.code?.toLowerCase()}-active`)
    const repo = useRepo(CurrencyRepository)

    async function input(active) {
        await repo.update({active, id: props.currency.id}, id)
    }
</script>

<template>
    <AppCol class="border border-dark d-flex flex-column m-1">
        <div class="d-flex">
            <AppInputGuesser :id="inputId" :field="field" :model-value="currency.active" @update:model-value="input"/>
            {{ currency.code }}
        </div>
        <span>1&nbsp;€ = {{ currency.base }}&nbsp;{{ currency.symbol }}</span>
        <span>{{ currency.name }}</span>
    </AppCol>
</template>
