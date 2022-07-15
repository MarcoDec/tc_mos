<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import AppComponentCard from './AppComponentCard.vue'
    import InfiniteLoading from 'v3-infinite-loading'
    import useNeeds from '../../../stores/needs/needs'
    const loaded = ref(false)

    const listDisplayed = useNeeds()

    onMounted(async () => {
        listDisplayed.fetch()

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
            <AppComponentCard
                v-for="(list, componentId) in listDisplayed.needsComponent"
                :key="componentId"
                :list="list"
                :component-id="componentId"/>
            <InfiniteLoading v-if="loaded" @infinite="listDisplayed.showComponent"/>
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
