<script setup>
    import AppShowProductTabAdmin from './tabs/AppShowProductTabAdmin.vue'
    import AppShowProductTabGeneral from './tabs/AppShowProductTabGeneral.vue'
    import AppShowProductTabLogistic from './tabs/AppShowProductTabLogistic.vue'
    import AppShowProductTabPrice from './tabs/AppShowProductTabPrice.vue'
    import AppShowProductTabProduction from './tabs/AppShowProductTabProduction.vue'
    import AppShowProductTabProject from './tabs/AppShowProductTabProject.vue'
    import AppTabFichiers from '../../../tab/AppTabFichiers.vue'
    import {useProductAttachmentStore} from '../../../../stores/project/product/productAttachement'
    import {useProductStore} from '../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idProduct = Number(route.params.id_product)
    const fetchProductAttachmentStore = useProductAttachmentStore()
    await fetchProductAttachmentStore.fetchByElement(idProduct)
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowProductTabGeneral/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-project"
            title="Project"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowProductTabProject/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Production"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowProductTabProduction/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <Suspense><AppShowProductTabLogistic/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-admin"
            title="Admin"
            icon="pallet"
            tabs="gui-start">
            <Suspense><AppShowProductTabAdmin/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <Suspense>
                <AppTabFichiers
                    attachment-element-label="product"
                    :element-api-url="`/api/products/${idProduct}`"
                    :element-attachment-store="fetchProductAttachmentStore"
                    :element-id="idProduct"
                    element-parameter-name="PRODUCT_ATTACHMENT_CATEGORIES"
                    :element-store="useProductStore"/>
            </Suspense>
        </AppTab>
        <AppTab
            id="gui-start-Prix"
            title="Prix"
            icon="circle-dollar-to-slot"
            tabs="gui-start">
            <Suspense><AppShowProductTabPrice/></Suspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
.gui-start-content {
    font-size: 14px;
}
</style>
