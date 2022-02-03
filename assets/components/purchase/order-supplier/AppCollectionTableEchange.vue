<script lang="ts" setup>
import {useRoute} from 'vue-router'
const route = useRoute()
const fields =
    [
      {
        label: 'Date',
        name: 'date',
        create: false,
        filter: false,
        delete: false
      },
      {
        label: 'Emetteur',
        name: 'date',
        create: false,
        filter: false,
        delete: false

      },
      {
        label: 'Destinataire',
        name: 'date',
        create: false,
        filter: false,
        delete: false

      },
      {
        label: 'Copie',
        name: 'date',
        create: false,
        filter: false,
        delete: false

      },
      {
        label: 'Commande jointe',
        name: 'date',
        create: false,
        filter: false,
        delete: false

      }
    ]

const items = [
]
</script>
<template>
  <div class="divEchange">
    <div class="divEmail">
      <div class="input-group">
        <span class="input-group-text">De:</span>
        <input type="text" class="form-control" aria-describedby="basic-addon1">
      </div>
      <div class="input-group">
        <span class="input-group-text">A:</span>
        <input type="text" class="form-control" aria-describedby="basic-addon1">
      </div>
      <div class="input-group">
        <span class="input-group-text">CC:</span>
        <input type="text" class="form-control" aria-describedby="basic-addon1">
      </div>
      <div class="input-group">
        <span class="input-group-text">Objet:</span>
        <input type="text" class="form-control" aria-describedby="basic-addon1">
      </div>
      <div class="input-group">
        <AppIframe />
      </div>

    </div>
    <div class="divBtnDow">
      <AppBtn variant="success" class="btnsend"> <Fa icon="paper-plane"/></AppBtn>

      <AppBtn> <Fa icon="download"/></AppBtn>
    </div>
    <div class="divtableEmail">
      <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
    </div>
  </div>
</template>
<style scoped>
.divEchange{
display: flex;
  flex-direction: row;
}
.divEmail{
  width: 45%;
  margin-right: 20px;
}
.divBtnDow{
  width: 5%;
}
.btnsend{
}
.divtableEmail{
  width: 60%;
}
</style>
