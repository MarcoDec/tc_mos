<script setup>
    import AppTable from '../../../table/AppTablePage.vue'
    import generateItems from '../../../../../stores/table/items'
    import {onMounted} from 'vue'
    import {useTableMachine} from '../../../../../machine'

    const machineSupp = useTableMachine('machine-supplier-items')
    const suppliersItems = generateItems('supplier-items-qualite')

    onMounted(async () => {
        suppliersItems.items = [
            {
                commande: 'commande',
                composant: 'composant1',
                copie: 'ccc',
                create: false,
                date: '2022-03-11',
                destinataire: 'ff',
                emetteur: 'eee',
                id: 1,
                sort: false,
                update: true
            },
            {
                commande: 'commande',
                composant: 'composant1',
                copie: 'ccc',
                create: false,
                date: '2022-03-11',
                destinataire: 'ff',
                emetteur: 'eee',
                id: 2,
                sort: false,
                update: true
            }
        ]
    })

    const fields = [
        {
            label: 'Composant',
            name: 'composant',
            sort: true,
            update: false
        },
        {
            label: 'Date Récéption',
            name: 'date',
            sort: true,
            update: false
        },
        {
            create: true,
            label: 'Emetteur',
            name: 'emetteur',
            sort: true,
            update: false
        },

        {
            create: true,
            filter: true,
            label: 'Destinataire',
            name: 'destinataire',
            sort: true,
            update: false
        },
        {
            label: 'Copie',
            name: 'copie',
            sort: true,
            update: false
        },
        {
            label: 'Commande jointe',
            name: 'commande',
            sort: true,
            update: false
        }
    ]
</script>

<template>
    <div class="divEchange">
        <div class="divEmail">
            <div class="input-group">
                <span class="input-group-text">De:</span>
                <input
                    type="text"
                    class="form-control"
                    aria-describedby="basic-addon1"/>
            </div>
            <div class="input-group">
                <span class="input-group-text">A:</span>
                <input
                    type="text"
                    class="form-control"
                    aria-describedby="basic-addon1"/>
            </div>
            <div class="input-group">
                <span class="input-group-text">CC:</span>
                <input
                    type="text"
                    class="form-control"
                    aria-describedby="basic-addon1"/>
            </div>
            <div class="input-group">
                <span class="input-group-text">Objet:</span>
                <input
                    type="text"
                    class="form-control"
                    aria-describedby="basic-addon1"/>
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
            <AppTable
                id="echange"
                :fields="fields"
                :machine="machineSupp"
                :store="suppliersItems"/>
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
.iframe {
  height: 80vh;
  transform: scale(0.75);
  transform-origin: 0 0;
}
</style>
