<script setup>
    import AppTreeFormGuesser from './form/AppTreeFormGuesser.vue'
    import AppTreeImg from './AppTreeImg.vue'

    defineProps({
        fields: {required: true, type: Array},
        machine: {required: true, type: Object},
        tree: {required: true, type: Object}
    })
</script>

<template>
    <AppCard :title="tree.title">
        <div class="row">
            <AppTreeFormGuesser :fields="fields" :machine="machine" :tree="tree" class="col"/>
            <div class="col">
                <AppTreeImg :tree="tree"/>
                <slot/>
            </div>
        </div>
    </AppCard>
</template>
