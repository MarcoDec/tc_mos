<script setup>

</script>

<template>
    <div>
        <h1>AppLabelCartonList</h1>
        <AppCardableTable
            :current-page="storeLabelCartons.currentPage"
            :fields="tabFields"
            :first-page="storeLabelCartons.firstPage"
            :items="itemsTable"
            :last-page="storeLabelCartons.lastPage"
            :min="AddForm"
            :next-page="storeLabelCartons.nextPage"
            :pag="storeLabelCartons.pagination"
            :previous-page="storeLabelCartons.previousPage"
            :user="roleuser"
            form="formLabelCartonCardableTable"
            @update="update"
            @deleted="deleted"
            @get-page="getPage"
            @trier-alphabet="trierAlphabet"
            @search="search"
            @cancel-search="cancelSearch"/>
    </div>
</template>

<style scoped>

</style>