<script lang="ts" setup>
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {defineEmits, defineProps, ref} from 'vue'
    import type {TreeItem} from '../../types/tree'

    const emit = defineEmits<{
        (e: 'ajout'): void,
        (e: 'update:formData', formData: FormValues): void
        (e: 'selected', item: TreeItem): void
    }>()
    defineProps<{item: TreeItem, fields: FormField, formData: FormValues}>()
    const selected = ref<TreeItem | null>(null)

    function addFamily(): void {
        emit('ajout')
    }

    function bascule(): void {
        selected.value = null
    }

    function input(item: PointerEvent | TreeItem): void {
        if (!(item instanceof PointerEvent)) {
            selected.value = item
            emit('selected', item)
        }
    }

    function update(formData: FormValues): void {
        emit('update:formData', formData)
    }
</script>

<template>
    <AppRow>
        <AppTree :item="item" class="col" @click="input"/>
        <AppCard class="bg-blue col">
            <div v-if="selected !== null" class="row">
                <AppBtn id="btnbascule" class="col-2 mb-2" variant="danger" @click="bascule">
                    <Fa id="lefticon" icon="angle-double-left"/>
                </AppBtn>
                <h2 class="col">
                    {{ selected?.code }}-{{ selected?.name }}
                </h2>
            </div>

            <AppForm :fields="fields" :values="formData" @submit="addFamily" @update:values="update">
                <template v-if="selected === null" #buttons>
                    <AppBtn id="btn" variant="success" @click="addFamily">
                        <Fa icon="plus"/>
                        Ajouter
                    </AppBtn>
                </template>
                <template v-else #buttons>
                    <AppBtn id="btn" variant="danger">
                        <Fa icon="trash"/>
                        Supprimer
                    </AppBtn>
                    <AppBtn id="btn" variant="success">
                        <Fa icon="pencil-alt"/>
                        Modifier
                    </AppBtn>
                </template>
            </AppForm>
        </AppCard>
    </AppRow>
</template>

<style>
    #btn {
        float: right;
        margin-right: 4px;
    }

    #btnbascule {
        width: 25px;
        height: 25px;
        margin-top: 10px;
    }

    #lefticon {
        margin-bottom: 3px;
        margin-left: -3px;
    }
</style>
