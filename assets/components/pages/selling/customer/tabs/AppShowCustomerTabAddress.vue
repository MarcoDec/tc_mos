<script setup>
    import AppCustomerBaseAddress from '../bottom/AppCustomerBaseAddress.vue'
    import AppCustomerOtherAddresses from '../bottom/AppCustomerOtherAddresses.vue'
    const props = defineProps({
        optionsCountries: {required: true, type: Array},
        customerId: {required: true, type: Number}
    })
</script>

<template>
    <div class="row">
        <div class="col-6">
            <AppCustomerBaseAddress
                :customer-id="customerId"
                :options-countries="optionsCountries"
                title=" Adresse principale"/>
        </div>
        <div class="col-6">
            <AppCustomerOtherAddresses
                :customer-id="customerId"
                :options-countries="optionsCountries"
            />
        </div>
    </div>

</template>
