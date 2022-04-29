<script lang="ts" setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import {
        useNamespacedActions,
        useNamespacedGetters,
        useNamespacedMutations
    } from 'vuex-composition-helpers'
    import AppProductCard from './AppProductCard.vue'
    import InfiniteLoading from 'v3-infinite-loading'

    const loaded = ref(false)

    const loading = useNamespacedActions('needs', ['load']).load

    const show = useNamespacedActions('needs', ['show']).show
    const initiale = useNamespacedMutations('needs', ['initiale']).initiale

    const listDisplayed = useNamespacedGetters('needs', [
        'listDisplayed'
    ]).listDisplayed

    onMounted(async () => {
        await loading()
        loaded.value = true
    })
    onUnmounted(async () => {
        initiale()
    })
</script>

<template>
    <AppRow>
        <div class="bcontent container">
            <hr/>
            <AppProductCard
                v-for="(list, productId) in listDisplayed"
                :key="productId"
                :list="list"
                :product-id="productId + 1"/>
        </div>
        <InfiniteLoading v-if="loaded" @infinite="show"/>
    </AppRow>
</template>

<style scoped>
.bcontent {
  margin-top: 10px;
}
.card {
  border: 1px solid #2a2a4b;
  box-shadow: black 3px 3px;
  margin-bottom: 10px;
}
.divUl {
  max-height: 100px;
  overflow: auto;
  border-color: black;
  background-color: rgb(255, 242, 120);
  font-size: 0.7em;
}
</style>
