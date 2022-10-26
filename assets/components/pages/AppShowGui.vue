<script setup>
    import {computed, ref} from 'vue'
    import AppTab from '../tab/AppTab.vue'
    import AppTabs from '../tab/AppTabs.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

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
</script>

<template>
    <div class="gui">
        <div class="gui-left">
            <div class="gui-card">
                <AppTabs id="gui-left">
                    <AppTab id="gui-left-main" active icon="bars" tabs="gui-left" title="Généralités"/>
                    <AppTab id="gui-left-files" icon="folder" tabs="gui-left" title="Fichiers"/>
                    <AppTab id="gui-left-quality" icon="certificate" tabs="gui-left" title="Qualité"/>
                    <AppTab id="gui-left-purchase-logistics" icon="boxes" tabs="gui-left" title="Achat/Logistique"/>
                    <AppTab id="gui-left-accounting" icon="file-invoice-dollar" tabs="gui-left" title="Comptabilité"/>
                    <AppTab id="gui-left-addresses" icon="map-marked-alt" tabs="gui-left" title="Adresses"/>
                    <AppTab id="gui-left-contacts" icon="address-card" tabs="gui-left" title="Contacts"/>
                </AppTabs>
            </div>
        </div>
        <div class="gui-right">
            <div class="gui-card"/>
        </div>
        <div class="gui-bottom">
            <div class="gui-card">
                <AppTabs id="gui-bottom">
                    <AppTab
                        id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom"
                        title="Fournitures"/>
                    <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>
                    <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>
                </AppTabs>
            </div>
            <hr class="gui-resizer" @mousedown="resize"/>
        </div>
    </div>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
