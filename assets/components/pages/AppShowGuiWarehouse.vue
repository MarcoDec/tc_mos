<script setup>
    import {computed, ref} from 'vue'
    import AppWarehouseFormShow from '../../router/pages/logistic/AppWarehouseFormShow.vue'
    import AppWarehouseListShow from '../../router/pages/logistic/AppWarehouseListShow.vue'
    import {useRouter, useRoute} from 'vue-router'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)
    const route = useRoute()
    const router = useRouter()

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    function resize(e) {
        const gui = e.target.parentElement.parentElement
        const height = gui.offsetHeight
        const top = gui.offsetTop

        function drag(position) {
            const ratio = (position.y - top) / height
            if (ratio >= 0.1 && ratio <= 0.9)
                guiRatio.value = ratio
        }

        function stopDrag() {
            gui.removeEventListener('mousemove', drag)
            gui.removeEventListener('mouseup', stopDrag)
        }

        gui.addEventListener('mousemove', drag)
        gui.addEventListener('mouseup', stopDrag)
    }

    function goBack() {
        router.push('warehouse-list')
    }
</script>

<template>
    <div class="titreEntrepot">
        <h1>
            <button class="btnEntrepotShow" @click="goBack">
                <Fa :icon="icon"/>
                {{ title }}
            </button>
        </h1>
    </div>
    <div class="gui">
        <div class="gui-left">
            <div class="gui-card">
                <AppSuspense><AppWarehouseFormShow v-if="route.name === 'warehouse-show'"/></AppSuspense>
            </div>
        </div>
        <div class="gui-right">
            <div class="gui-card"/>
        </div>
        <div class="gui-bottom">
            <div class="gui-card">
                <AppSuspense><AppWarehouseListShow v-if="route.name === 'warehouse-show'"/></AppSuspense>
            </div>
            <hr class="gui-resizer" @mousedown="resize"/>
        </div>
    </div>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
        margin-top: 62px;
    }
    .btnEntrepotShow{
        border-color: transparent;
        background-color: transparent;
    }
</style>
