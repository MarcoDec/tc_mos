<script lang="ts" setup>
import {defineProps, onMounted, onUnmounted, ref} from 'vue'
import {useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'
import type {Getters} from '../../store/gui/getters'
import {MutationTypes} from '../../store/gui/mutations'
import type {Mutations} from '../../store/gui/mutations'
import type {Tabs} from '../../types/bootstrap-5'

defineProps<{cssClass?: string}>()

const tabs = ref<Tabs[]>([
  {name: 'Généralistés1', isActive: true},
  {name: 'Fichiers', isActive: false},
  {name: 'Qualité', isActive: false},
  {name: 'Achat/Logistique', isActive: false}
])

const {tabHeightpx, innerHeightpx} = useNamespacedGetters<Getters>('gui', ['tabHeightpx', 'innerHeightpx'])

const resize = useNamespacedMutations<Mutations>('gui', [MutationTypes.RESIZE])[MutationTypes.RESIZE]
function setActive(current: Tabs): void {
  tabs.value = tabs.value.map(tab => ({
    ...tab,
    isActive: tab === current
  }))

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
    <div class="tabsIcon">
        <div class="tab-header">
            <ul id="myTab" class="flex-column nav nav-tabs" role="tablist">
                <li v-for="tab in tabs" class="nav-item" role="presentation">
                    <a
                        id="generaliste-tab" :class="{active: tab.isActive}" class="nav-link" data-bs-toggle="tab" href="#" role="tab"
                        aria-controls="home" aria-selected="true" @click="setActive(tab)">
                        <Fa icon="home"/> {{ tab.name }}
                    </a>
                </li>
            </ul>
        </div>
        <div id="myTabContent" class="tab-content">
            <div v-for="tab in tabs" id="generaliste" :class="{active: tab.isActive}" class="fade show tab-pane" role="tabpanel" aria-labelledby="generaliste-tab">
                {{ tab.name }}
            </div>
        </div>
    </div>
</template>




<style scoped>
.tabsIcon{
  display: flex;
  flex-direction: row;
}
.nav{
  flex-wrap: initial;
}
.nav-item {
  background: #007bff;
  width: 174px;
}
.nav-link {
  color: #007bff;
  background-color: white;
}

.nav-item[data-v-0ec7f043] {
  background: none !important;
}

.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
  color: white;
  background-color: #007bff;
  border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs {
  border-bottom: 0px solid #dee2e6;
}

.tab-header {
  font-size: .8rem;
  max-height: v-bind('tabHeightpx');
  overflow: hidden;
  padding: 0.75rem 1.25rem;
  padding-bottom: 27.75rem;
  margin-bottom: 0;
  background-color: rgba(0, 0, 0, 0.03);
  border-bottom: 0px solid rgba(0, 0, 0, 0.125);
}

@media only screen and (min-width: 1100px) and (max-width: 1600px) {
  .nav-item {
    width: 150px;
  }
}
</style>
