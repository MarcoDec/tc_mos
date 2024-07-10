<script setup>
    import AppPricesTablePage from "../../../prices/AppPricesTablePage.vue"
    import useUser from "../../../../../stores/security";
    defineProps({
        product: {
            type: String,
            required: true
        }
    })
    const user = useUser()
    const isSellingAdmin = user.isSellingAdmin
    const isSellingWriter = user.isSellingWriter
    const rights = {
        main: {
            add: isSellingWriter,
            update: isSellingWriter,
            delete: isSellingAdmin
        },
        price: {
            add: isSellingWriter,
            update: isSellingWriter,
            delete: isSellingAdmin
        }
    }
</script>

<template>
    <AppPricesTablePage
        :product="product"
        :rights="rights"
        title="Grilles de prix Produit"
    />
</template>
