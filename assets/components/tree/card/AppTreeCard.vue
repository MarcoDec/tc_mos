<script setup>
    import AppTreeFormGuesser from './form/AppTreeFormGuesser.vue'
    import AppTreeImg from './AppTreeImg.vue'

    defineProps({machine: {required: true, type: Object}, tree: {required: true, type: Object}})
</script>

<template>
    <AppCard :title="tree.title">
        <div class="row">
            <AppTreeFormGuesser :machine="machine" :tree="tree" class="col"/>
            <AppTreeImg :tree="tree" class="col"/>
        </div>
    </AppCard>
</template>
