<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import {useRepo, useRouter} from '../../../composition'
    import AppCurrencies from '../../../components/management/currency/AppCurrencies.vue'
    import {CurrencyRepository} from '../../../store/modules'

    const {id} = useRouter()
    const mount = ref(false)
    const repo = useRepo(CurrencyRepository)

    onMounted(async () => {
        await repo.load(id)
        mount.value = true
    })

    onUnmounted(() => {
        repo.destroyAll(id)
    })
</script>

<template>
    <AppCurrencies v-if="mount"/>
</template>
