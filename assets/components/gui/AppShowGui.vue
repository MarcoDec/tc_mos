<script lang="ts" setup>
    import type {Getters, Mutations} from '../../store/gui'
    import {onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import {useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'
    import type {DeepReadonly} from '../../types/types'
    import {MutationTypes} from '../../store/gui'

    const gui = ref<DeepReadonly<HTMLDivElement>>()
    const {
        bottomHeightPx,
        endWidthPx,
        guiBottom,
        heightPx,
        innerStartHeightPx,
        innerWidthPx,
        marginEndPx,
        paddingPx,
        startWidthPx,
        topHeightPx,
        marginTopPx,
        widthPx
    } = useNamespacedGetters<Getters>('gui', [
        'bottomHeightPx',
        'endWidthPx',
        'guiBottom',
        'heightPx',
        'innerStartHeightPx',
        'innerWidthPx',
        'marginEndPx',
        'paddingPx',
        'startWidthPx',
        'topHeightPx',
        'marginTopPx',
        'widthPx'
    ])
    const {[MutationTypes.RESIZE]: resize} = useNamespacedMutations<Mutations>('gui', [MutationTypes.RESIZE])

    function resizeHandler(): void {
        if (typeof gui.value !== 'undefined')
            resize(gui.value)
    }

    watchPostEffect(() => {
        resizeHandler()
    })

    onMounted(() => {
        window.addEventListener('resize', resizeHandler)
    })

    onUnmounted(() => {
        window.removeEventListener('resize', resizeHandler)
    })
</script>

<template>
    <div ref="gui" class="bg-secondary gui">
        <div class="gui-top">
            <AppShowGuiCard
                :height="topHeightPx"
                :inner-width="innerWidthPx"
                :margin-end="marginEndPx"
                :width="startWidthPx"
                bg-variant="info"
                class="gui-card">
                <AppTabs class="gui-start-content">
                    <AppTab id="main" active title="Général">
                        <div>
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur dolores magnam
                            temporibus? Dicta ducimus esse excepturi id magnam obcaecati saepe similique ullam veritatis
                            voluptates! Animi laborum neque numquam saepe sunt.
                        </div>
                        <div>
                            Autem beatae dolor doloremque error eum eveniet ex iste laudantium maiores numquam odio
                            pariatur perferendis, quae quam, qui quibusdam quo sequi similique sunt ullam. Explicabo
                            iure necessitatibus nisi saepe sequi!
                        </div>
                        <div>
                            Architecto, at commodi consectetur ea fugit, harum id illo ipsum labore laboriosam officiis
                            repellendus suscipit, unde. Distinctio et eum fuga fugit harum nostrum quae quaerat, saepe
                            totam veritatis. Consequatur, vero!
                        </div>
                        <div>
                            Alias animi commodi cumque, dolorem ea error fugiat ipsam nam, neque odio odit officiis
                            recusandae sed tenetur ut, veniam voluptatem. Ad, delectus, possimus! Cum earum eligendi
                            inventore laborum maiores sapiente.
                        </div>
                        <div>
                            Adipisci consequuntur, cumque enim expedita nulla obcaecati officia ullam! At, eum eveniet
                            minus officiis optio placeat sequi velit? Consequuntur deleniti dolorem expedita iusto
                            laudantium officia omnis perspiciatis ratione rerum veritatis?
                        </div>
                        <div>
                            Ab aliquam doloremque esse id maxime nam quaerat repudiandae. Asperiores commodi
                            consequatur delectus dolor, dolorem doloremque dolorum eius, esse excepturi explicabo
                            laudantium libero pariatur, porro quae reiciendis vero voluptas voluptatibus.
                        </div>
                        <div>
                            Aliquam aliquid blanditiis cupiditate dolorem doloribus dolorum ducimus eaque eligendi enim
                            eos ex facere fuga in iste itaque iure libero magni modi non quaerat quis quos, rem tenetur
                            ullam voluptate.
                        </div>
                        <div>
                            Aliquam assumenda blanditiis consequuntur cum deserunt doloremque eum ex, inventore laborum
                            molestias mollitia totam unde vel voluptate voluptatibus. Animi culpa dolore fugiat
                            laboriosam magni nemo odit perspiciatis placeat provident suscipit.
                        </div>
                        <div>
                            Aperiam blanditiis ducimus eveniet inventore minima mollitia obcaecati, quis quo
                            reprehenderit tempore. Adipisci aliquid cupiditate doloribus harum laboriosam nesciunt,
                            nulla, quas quis, rem soluta suscipit veniam! Atque perferendis quasi quis.
                        </div>
                        <div>
                            Accusamus alias, aperiam architecto at cum cumque cupiditate ea eaque earum eius id,
                            incidunt labore laudantium, modi natus nobis nostrum omnis possimus quas quasi quod
                            recusandae tempora unde vero vitae?
                        </div>
                    </AppTab>
                    <AppTab id="logistics" title="Logistique"/>
                </AppTabs>
            </AppShowGuiCard>
            <AppShowGuiCard
                :height="topHeightPx"
                :inner-width="innerWidthPx"
                :width="endWidthPx"
                bg-variant="warning"
                class="gui-card gui-end"/>
        </div>
        <component
            :is="guiBottom"
            :height="bottomHeightPx"
            :inner-width="innerWidthPx"
            :margin-top="marginTopPx"
            :width="innerWidthPx"
            bg-variant="danger"
            class="gui-card"/>
    </div>
</template>

<style scoped>
    .gui {
        max-width: v-bind('widthPx');
        min-width: v-bind('widthPx');
        padding: v-bind('paddingPx');
        width: v-bind('widthPx');
    }

    .gui-card {
        padding: v-bind('paddingPx');
    }

    .gui-start-content {
        height: v-bind('innerStartHeightPx');
        max-height: v-bind('innerStartHeightPx');
        min-height: v-bind('innerStartHeightPx');
    }

    @media (max-width: 1140px) {
        .gui-end {
            margin-top: v-bind('marginTopPx') !important;
        }
    }

    @media (min-width: 1140px) {
        .gui {
            height: v-bind('heightPx');
            max-height: v-bind('heightPx');
            min-height: v-bind('heightPx');
        }
    }
</style>
