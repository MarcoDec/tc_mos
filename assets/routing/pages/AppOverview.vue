<script lang="ts" setup>
    import type {Getters} from '../../store/overview'
    import {useNamespacedGetters} from 'vuex-composition-helpers'

    const {all, count} = useNamespacedGetters<Getters>('overview', ['all', 'count'])
    console.log('all', all.value)
    console.log('count', count.value)
</script>

<template>
    <div class="bg-secondary card">
        <div class="row">
            <div class="col">
                <div v-for="(items, index) in all" :key="index" class="col">
                    <!-- <span v-for="item in items"> {{item}} </span> -->
                    <span> {{ items[0] }}: {{ items[1] }}  </span>
                </div>
            </div>
            <div class="col">
                count: {{ count }}
            </div>
        </div>
    </div>
</template>
