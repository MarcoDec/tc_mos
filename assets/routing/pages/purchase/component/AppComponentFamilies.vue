<script lang="ts" setup>
    import type {TreeItem} from '../../../../types/tree'

    const item: TreeItem = {
        children: [
            {
                children: [
                    {icon: 'file-alt', id: 2, label: 'CAB — Fil'},
                    {id: 3, label: 'CAB — Fil'}
                ],
                id: 1,
                label: 'CAB — Câbles'
            },
            {
                children: [
                    {id: 6, label: 'FIX — Agrafe'},
                    {id: 7, label: 'FIX — Colliers'}
                ],
                id: 5,
                label: 'FIX — Fixations'
            }
        ],
        id: 0,
        label: ''
    }
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow :item="item">
        <form>
            <div class="mb-3 row">
                <label class="col-2 form-label" for="parent">Parent</label>
                <div class="col">
                    <input id="parent" class="form-control"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-2 form-label" for="code">Code</label>
                <div class="col">
                    <input id="code" class="form-control"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-2 form-label" for="name">Nom</label>
                <div class="col">
                    <input id="name" class="form-control"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-2 form-label" for="copperable">Gestion du cuivre</label>
                <div class="col">
                    <input id="copperable" class="form-control"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-2 form-label" for="customsCode">Code douanier</label>
                <div class="col">
                    <input id="customsCode" class="form-control"/>
                </div>
            </div>
        </form>
    </AppTreeRow>
</template>
