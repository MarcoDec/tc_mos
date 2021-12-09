<script lang="ts" setup>
    import {defineProps, ref} from 'vue'
    import { FormValues } from '../../types/bootstrap-5'
    import type {TreeItem} from '../../types/tree'

    defineProps<{item: TreeItem, fields: FormField , formData:FormValues}>()

    const emit = defineEmits<{
        (e: 'ajout'): void,
        (e: 'update:formData',formData:FormValues):void
        (e: 'selected', item : TreeItem):void
    }>()


    const selected = ref<TreeItem | null>(null)


    
    function input(item: PointerEvent | TreeItem): void {
        if (!(item instanceof PointerEvent)){
            selected.value = item
            console.log('selected', selected.value);
            emit('selected',item)
        }
            
    }
    function bascule ():void {
        selected.value = null

    }
    function update (formData:FormValues):void{
        emit('update:formData',formData)
    }
    function addFamily ():void {
        emit('ajout')
    }
</script>

<template>
    <AppRow>
        <AppTree :item="item" class="col" @click="input"/>
        <AppCard class="bg-blue col">
            <div class="row" v-if="selected!==null">
                <AppBtn class="col-2 mb-2  "  variant="danger" id="btnbascule" @click="bascule">
                  <Fa id="lefticon" icon="angle-double-left"/>
                </AppBtn>
                <h2 class="col" >{{ selected?.code }}-{{ selected?.name }}</h2>
            </div>
           
        <AppForm :values="formData" :fields="fields" @submit="addFamily" @update:values="update" >
            <template #buttons v-if="selected===null"> 
             <AppBtn variant="success"  id="btn"  @click="addFamily" >
                <Fa icon="plus"/> Ajouter
             </AppBtn>
            </template>
            <template #buttons v-else> 
             <AppBtn variant="danger"  id="btn" >
                  <Fa icon="trash"/> Supprimer
             </AppBtn>
             <AppBtn variant="success"  id="btn" >
                  <Fa icon="pencil-alt"/> Modifier
                </AppBtn>
            </template>
        </AppForm>  
        </AppCard>
    </AppRow>
</template>
<style>
#btn{
    float: right;
    margin-right: 4px;
}
#btnbascule{
    width: 25px;
    height: 25px;
    margin-top: 10px;
}
#lefticon{
    margin-bottom: 3px;
    margin-left: -3px;
}
</style>