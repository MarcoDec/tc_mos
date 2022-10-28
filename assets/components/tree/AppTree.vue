<script setup>
    defineProps({tree: {required: true, type: Object}})
</script>

<template>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <AppTreeNodes :nodes="tree.roots"/>
        </div>
    </div>
</template>
