<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import AppProductCard from './AppProductCard.vue'
    import InfiniteLoading from 'v3-infinite-loading'
    import useNeeds from '../../../stores/needs/needs'

    const loaded = ref(false)

    const listDisplayed = useNeeds()

    onMounted(async () => {
        await listDisplayed.fetchProducts()
        listDisplayed.showProduct()
        // console.log('needsProduct', listDisplayed.needsProduct)
        loaded.value = true

    })
    onUnmounted(async () => {
        listDisplayed.initiale()
    })
</script>

<template>
    <AppRow>
        <div class="bcontent container">
            <hr/>
            <div v-if="loaded">
                <AppProductCard
                    v-for="(list, productId) in listDisplayed.needsProduct"
                    :key="`product_chart_${productId}`"
                    :list="list"
                    :product-id="productId"/>
                <InfiniteLoading v-if="loaded" @infinite="listDisplayed.showProduct"/>
            </div>
            <div v-else>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
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
