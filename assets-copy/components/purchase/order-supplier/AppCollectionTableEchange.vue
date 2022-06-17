<script lang="ts" setup>
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const fields = [
        {
            create: false,
            delete: false,
            filter: false,
            label: 'Date',
            name: 'date'

        },
        {
            create: false,
            delete: false,
            filter: false,
            label: 'Emetteur',
            name: 'emmetteur'

        },
        {
            create: false,
            delete: false,
            filter: false,
            label: 'Destinataire',
            name: 'des'

        },
        {
            create: false,
            delete: false,
            filter: false,
            label: 'Copie',
            name: 'copie'

        },
        {
            create: false,
            delete: false,
            filter: false,
            label: 'Commande jointe',
            name: 'cmd'

        }
    ]

    const items = [
        {}
    ]
</script>

<template>
    <div class="divEchange">
        <div class="divEmail">
            <div class="input-group">
                <span class="input-group-text">De:</span>
                <input type="text" class="form-control" aria-describedby="basic-addon1"/>
            </div>
            <div class="input-group">
                <span class="input-group-text">A:</span>
                <input type="text" class="form-control" aria-describedby="basic-addon1"/>
            </div>
            <div class="input-group">
                <span class="input-group-text">CC:</span>
                <input type="text" class="form-control" aria-describedby="basic-addon1"/>
            </div>
            <div class="input-group">
                <span class="input-group-text">Objet:</span>
                <input type="text" class="form-control" aria-describedby="basic-addon1"/>
            </div>
            <div class="input-group">
                <iframe
                    class="iframe"
                    src="http://localhost:8000/supplierOrder/previewPDF"
                    title="prévisualisation commande"
                    width="110%"
                    height="100%"/>
            </div>
        </div>
        <div class="divBtnDow">
            <AppBtn variant="success" class="btnsend">
                <Fa icon="paper-plane"/>
            </AppBtn>

            <AppBtn>
                <Fa icon="download"/>
            </AppBtn>
        </div>
        <div class="divtableEmail">
            <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
        </div>
    </div>
</template>

<style scoped>
.divEchange {
  display: flex;
  flex-direction: row;
}

.divEmail {
  width: 45%;
  margin-right: 20px;
}

.divBtnDow {
  width: 5%;
}

.divtableEmail {
  width: 60%;
}
.iframe{
  height: 80vh;
  transform: scale(0.75);
  transform-origin:  0 0;
}
</style>
