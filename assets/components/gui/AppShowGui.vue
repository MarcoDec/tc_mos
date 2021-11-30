<script lang="ts" setup>
import {defineProps, onMounted, onUnmounted} from 'vue'
import {
  useNamespacedActions,
  useNamespacedGetters,
  useNamespacedMutations, useNamespacedState

} from 'vuex-composition-helpers'
import type {Actions} from '../../store/gui/card/actions'
import AppCol from '../bootstrap-5/layout/AppCol.vue'
import AppContainer from '../bootstrap-5/layout/AppContainer.vue'
import AppRow from '../bootstrap-5/layout/AppRow.vue'
import AppShowGuiCard from './AppShowGuiCard.vue'
import AppShowGuiTabs from './AppShowGuiTabs.vue'
import AppShowGuiTabsIcon from './AppShowGuiTabsIcon.vue'
import {ActionTypes} from '../../store/gui/card/actions'
import type {Getters} from '../../store/gui/getters'
import type {GettersCard} from '../../store/gui/card/getters'
import {MutationTypes} from '../../store/gui/mutations'
import type {Mutations} from '../../store/gui/mutations'
import {MutationTypes as MutationTypesCard} from '../../store/gui/card/mutations'
import type {Mutations as MutationsCard} from '../../store/gui/card/mutations'

defineProps<{ cssClass?: string }>()


const {containerWidth} = useNamespacedGetters<Getters>('gui', ['containerWidth'])
const {
  heightBottomCssVars,
  innerHeightBottomCssVars,
  bottomHeight,
  topHeight,
  containerHeight,
  topCssVars
} = useNamespacedGetters<GettersCard>('card', ['heightBottomCssVars', 'innerHeightBottomCssVars', 'bottomHeight', 'topHeight', 'containerHeight', 'topCssVars'])

const resize = useNamespacedMutations<Mutations>('gui', [MutationTypes.RESIZE])[MutationTypes.RESIZE]
const startY = useNamespacedMutations<MutationsCard>('card', [MutationTypesCard.STARTY])[MutationTypesCard.STARTY]
const resizerClick = useNamespacedMutations<MutationsCard>('card', [MutationTypesCard.RESIZECLICK])[MutationTypesCard.RESIZECLICK]
const onlyOne = useNamespacedMutations<MutationsCard>('card', [MutationTypesCard.ONLYONE])[MutationTypesCard.ONLYONE]
const loading = useNamespacedMutations<MutationsCard>('card', [MutationTypesCard.LOADING])[MutationTypesCard.LOADING]


const doDrag = useNamespacedActions<Actions>('card', [ActionTypes.DODRAG])[ActionTypes.DODRAG]
const starty = useNamespacedActions<Actions>('card', [ActionTypes.STARTY])[ActionTypes.STARTY]
const startx = useNamespacedActions<Actions>('card', [ActionTypes.STARTX])[ActionTypes.STARTX]

function initDrag(event: MouseEvent) {
  starty(event)
  startx()
  document.documentElement.addEventListener('mousemove', doDrag, false)
  document.documentElement.addEventListener('mouseup', stopDrag, false)
}


function stopDrag() {
  document.documentElement.removeEventListener('mousemove', doDrag, false)
  document.documentElement.removeEventListener('mouseup', stopDrag, false)
}


onMounted(() => {
  window.addEventListener('resize', resize)
  resize()

})

onUnmounted(() => {

  window.removeEventListener('resize', resize)
})
</script>

<template>
  <AppRow class="gui">
    <AppCol>
      <AppContainer class="gui-container mt-2" fluid>
        <AppRow :class="cssClass">
          <AppShowGuiCard
              :css-style="topHeight"
              bg-variant="info"
              class="gui-up-left"
              css-class="gui-top">
            <AppShowGuiTabs/>
          </AppShowGuiCard>
          <AppShowGuiCard
              :css-style="topHeight"
              bg-variant="warning"
              class="gui-up-left"
              css-class="gui-top"/>
        </AppRow>
        <hr class="resizer" @click="resizerClick" @mousedown="initDrag"/>
        <AppRow>
          <AppShowGuiCard
              :css-style="bottomHeight"
              bg-variant="success"
              class="gui-down"
              css-class="gui-bottom">
            <AppShowGuiTabsIcon/>
          </AppShowGuiCard>
        </AppRow>
      </AppContainer>
    </AppCol>
  </AppRow>
</template>


<style lang="scss" scoped>
.gui {
  width: v-bind(containerWidth);
}

.gui-container {
  background-color: #cecdcd;
  border-radius: 15px;
}

.gui-up-left {
  padding: 0px 0px 0px 0px;
  overflow: hidden;
  border: 5px solid #17a2b8;
  border-radius: 10px;
  margin-right: 5px;
  margin-bottom: 5px;
  margin-top: 5px;
  // height: v-bind(topCssVars);

}

.gui-up-right {
  padding: 0px 0px 0px 0px;
  overflow: hidden;
  border-radius: 10px;
  border: 5px solid #ffc107;
  margin-bottom: 5px;
  margin-top: 5px;
}

.gui-down {
  padding: 0px 0px 0px 0px;
  overflow: hidden;
  border-radius: 10px;
  border: 5px solid #28a745;
  margin-bottom: 5px;
  height: v-bind(heightBottomCssVars);
  max-height: v-bind(innerHeightBottomCssVars);
}

.resizer {
  background-color: white;
  margin: 0px;
  margin-top: 5px;
  //border:3px black solid;
  //cursor: row-resize;
}

@media only screen and (min-width: 1025px) and (max-width: 1600px) {
  .resizer {
    background-color: white;
    margin: 0px;
    margin-top: 5px;
    border: 3px black solid;
    cursor: row-resize;
  }
}
</style>
