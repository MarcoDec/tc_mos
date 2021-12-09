<script lang="ts" setup>
import * as Cookies from '../../../../cookie'
import {
  useNamespacedActions,
  useNamespacedGetters,
  useNamespacedMutations,
} from "vuex-composition-helpers";
import {Actions, ActionTypes, Mutations, MutationTypes} from "../../../../store/management/currency";
import {onMounted, ref} from "vue";
import AppForm from "../../../../components/bootstrap-5/form/AppForm.vue";
import AppCol from "../../../../components/bootstrap-5/layout/AppCol.vue";
import {FormField} from "../../../../types/bootstrap-5";

const fields: FormField[] = [
  {label: 'Actif', name: 'active', type: 'switch'},
  {label: 'Code', name: 'code'},
  {label: 'Nom', name: 'nom'},
]

const selected = ref()

onMounted(async () => {
  await useNamespacedActions<Actions>('currency', [ActionTypes.FETCH_CURRENCY])[ActionTypes.FETCH_CURRENCY]()

});
const list = useNamespacedGetters('currency', ['getListCurrency']).getListCurrency
const filter = useNamespacedGetters('currency', ['filters']).filters
const actions = useNamespacedActions<Actions>('currency', [ActionTypes.UPDATE_DATA])[ActionTypes.UPDATE_DATA]
const search = useNamespacedMutations<Mutations>('currency', [MutationTypes.FILTER])[MutationTypes.FILTER]

async function click(item: PointerEvent) {
  if (!(item instanceof PointerEvent))

    selected.value = item
  Cookies.set('idItem', selected.value)
  await actions({
    active: !list.value[selected.value - 1].active
  })
}

</script>

<template>
  <AppRow>
    <AppRow>
      <Fa icon="euro-sign" class="iconEuro"/>
      <h1 class="textD">Devise</h1>
    </AppRow>
    <AppRow>
      <AppCol>
        <AppForm :values="filter" @update:values="search" :fields="fields" class="formDevise"/>
      </AppCol>

    </AppRow>
  </AppRow>
  <AppRow class="divDevise">
    <AppCol v-for="list in list">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" :checked="list.active"
               @click="click(list.id)"
        >
        <span>{{ list.code }}</span><br>
        <span>1 € = {{ list.rate }} {{ list.symbol }}</span><br>
        <span>{{ list.name }}</span>
      </div>
    </AppCol>
  </AppRow>

</template>


<style scoped>
.row > * {
  width: auto;
}

.textD {
  font-size: 3.07rem;
}

.divDevise {
  border: 1px solid #2a2a4b;
  display: grid;
  grid-gap: 10px;
  grid-template-columns: repeat(5, 1fr);
  margin-right: -15px;
  margin-left: -15px;
}

.formDevise {
  display: flex;
  flex-direction: row;
  border: 1px solid #2a2a4b;
}

.iconEuro {
  font-size: 3.07rem;
  margin-top: 7px;
}


</style>