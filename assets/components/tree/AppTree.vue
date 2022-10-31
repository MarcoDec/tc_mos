<script setup>
    import {assign, useMachine} from '../../composable/xstate'
    import AppTreeCard from './card/AppTreeCard.vue'
    import {useRoute} from 'vue-router'

    defineProps({tree: {required: true, type: Object}})

    const route = useRoute()
    const machine = useMachine({
        context: {violations: []},
        id: route.name,
        initial: 'form',
        states: {
            error: {on: {submit: {target: 'loading'}}},
            form: {on: {submit: {actions: ['reset'], target: 'loading'}}},
            loading: {
                on: {
                    fail: {actions: ['violate'], target: 'error'},
                    success: {target: 'form'}
                }
            }
        }
    }, {
        actions: {
            reset: assign({violations: []}),
            violate: assign((context, {violations}) => ({violations}))
        }
    })
</script>

<template>
    <AppOverlay :spinner="machine.state.value.matches('loading')" class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <AppTreeNodes :nodes="tree.roots"/>
        </div>
        <AppTreeCard :machine="machine" :tree="tree" class="col"/>
    </AppOverlay>
</template>
