<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../MyTree.vue'
    import generateProduct from '../../../../stores/product/product'
    import {useIncotermStore} from '../../../../stores/incoterm/incoterm'
    import useOptions from '../../../../stores/option/options'
    import {useProductAttachmentStore} from '../../../../stores/product/productAttachement'
    import {useProductStore} from '../../../../stores/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idProduct = route.params.id_product

    const isError = ref(false)
    const isError2 = ref(false)
    const violations = ref([])
    const violations2 = ref([])
    const fecthOptions = useOptions('units')
    const fetchProductStore = useProductStore()
    const fetchProductAttachmentStore = useProductAttachmentStore()
    const fetchIncotermStore = useIncotermStore()

    await fetchProductStore.fetchOne(idProduct)
    await fetchProductStore.fetchProductFamily()
    await fecthOptions.fetchOp()
    await fetchProductAttachmentStore.fetch()
    await fetchIncotermStore.fetch()

    const managedCopperValue = ref(fetchProductStore.product.managedCopper)
    const productAttachment = computed(() =>
        fetchProductAttachmentStore.productAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(),
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: productAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${productAttachment.value.length})`
        }
        return data
    })
    const optionsKind = [
        {
            text: 'Série',
            value: 'Série'
        },
        {text: 'Prototype', value: 'Prototype'},
        {text: 'EI', value: 'EI'},
        {text: 'Pièce de rechange', value: 'Pièce de rechange'}
    ]
    const optionsIncoterm = computed(() =>
        fetchIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            const optionList = {text, value}
            return optionList
        }))
    const optionsUnit = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.value
            const optionList = {text, value}
            return optionList
        }))
    const optionsUnitText = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.text
            const optionList = {text, value}
            return optionList
        }))
    const optionsProductFamily = computed(() =>
        fetchProductStore.productsFamily.map(op => {
            const text = op.fullName
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const Géneralitésfields = [
        {
            label: 'Famille',
            name: 'family',
            options: {
                label: value =>
                    optionsProductFamily.value.find(option => option.type === value)
                        ?.text ?? null,
                options: optionsProductFamily.value
            },
            type: 'select'
        },

        {label: 'Note', name: 'notes', type: 'textarea'},
        {label: 'Gestion du cuivre', name: 'managedCopper', type: 'boolean'}
    ]
    const Projectfields = [
        {label: 'Date Fin', name: 'getEndOfLife', type: 'date'},
        {label: 'maxProto', measure: {code: 'U', value: 'valeur'}, name: 'maxProto', type: 'measure'},
        {label: 'Prix (champ calculé)', measure: {code: 'Devise', value: 'valeur'}, name: 'price', type: 'measure'},
        //   { label: "Prix Cuivre", name: "priceWithoutCopper", type: "measure" },
        {
            label: 'Prix Transfer Fournisseur (champ calculé)',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'transfertPriceSupplies',
            type: 'measure'
        },
        {
            label: 'Prix Transfer Work (champ calculé)',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'transfertPriceWork',
            type: 'measure'
        }
    ]
    const ProjectfieldsPrix = [
        {
            label: 'Prix Cuivre (champ calculé)',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'priceWithoutCopper',
            type: 'measure'
        }
    ]
    const Productionfields = [
        {label: 'Duration Auto', measure: {code: 'h', value: 'valeur'}, name: 'autoDuration', type: 'measure'},
        {label: 'Duration Manual', measure: {code: 'h', value: 'valeur'}, name: 'manualDuration', type: 'measure'},
        {label: 'Production Min', measure: {code: 'U', value: 'valeur'}, name: 'minProd', type: 'measure'},
        {label: 'Production Delay', measure: {code: 'jr', value: 'valeur'}, name: 'productionDelay', type: 'measure'},
        {
            label: 'Volume prévisionnel (champ calculé)',
            measure: {code: 'U', value: 'valeur'},
            name: 'forecastVolume',
            type: 'measure'
        },
        {label: 'Packaging', measure: {code: 'Devise', value: 'valeur'}, name: 'packaging', type: 'measure'},
        {label: 'Packaging Kind', name: 'packagingKind', type: 'text'}
    ]
    const Logistiquefields = [
        {label: 'Code douanier', name: 'customsCode', type: 'text'},
        {
            label: 'Incoterms',
            name: 'incoterms',
            options: {
                label: value =>
                    optionsIncoterm.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsIncoterm.value
            },
            type: 'select'
        },
        {
            label: 'Stock Min',
            name: 'minStock',
            options: {
                label: value =>
                    optionsUnitText.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsUnitText.value
            },
            type: 'measureSelect'
        },
        {
            label: 'Delivery Min',
            name: 'minDelivery',
            options: {
                label: value =>
                    optionsUnitText.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsUnitText.value
            },
            type: 'measureSelect'
        },
        {
            label: 'Poids',
            name: 'weight',
            options: {
                label: value =>
                    optionsUnitText.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsUnitText.value
            },
            type: 'measureSelect'
        }
    ]
    const Adminfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Code', name: 'code', type: 'text'},
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'select'
        },
        {label: 'Index', name: 'index', type: 'text'},
        {label: 'Index interne', name: 'internalIndex', type: 'text'},
        {
            label: 'Kind',
            name: 'kind',
            options: {
                label: value =>
                    optionsKind.find(option => option.type === value)?.text ?? null,
                options: optionsKind
            },
            type: 'select'
        }
    ]
    const Fichiersfields = [
        {label: 'Categorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]

    async function updateGeneral(value) {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)

        const data = {
            family: formData.get('family'),
            notes: formData.get('notes')
            //managedCopper: JSON.parse(formData.get("managedCopper")),
        }

        const item = generateProduct(value)
        await item.updateMain(data)
        await fetchProductStore.fetchOne(idProduct)
    }
    async function updateAdmin(value) {
        const form = document.getElementById('addAdmin')
        const formData = new FormData(form)
        const data = {
            code: formData.get('code'),
            index: formData.get('index'),
            // internalIndex: formData.get("internalIndex"),
            kind: formData.get('kind'),
            name: formData.get('name')
        }

        const item = generateProduct(value)
        await item.updateAdmin(data)
        await fetchProductStore.fetchOne(idProduct)
    }
    async function updateProject(value) {
        const form = document.getElementById('addProject')
        const formData = new FormData(form)

        const data = {
            endOfLife: formData.get('getEndOfLife')
            // maxProto: {
            //   code: formData.get("maxProto-code"),
            //   value: JSON.parse(formData.get("maxProto-value")),
            // },
        }

        const item = generateProduct(value)
        await item.updateProject(data)
        await fetchProductStore.fetchOne(idProduct)
    }
    async function updateProduction(value) {
        const form = document.getElementById('addProduction')
        const formData = new FormData(form)

        const data = {
            autoDuration: {
                //code: formData.get("autoDuration-code"),
                value: JSON.parse(formData.get('autoDuration-value'))
            },
            // forecastVolume: {
            //   code: formData.get("forecastVolume-code"),
            //   value: JSON.parse(formData.get("forecastVolume-value")),
            // },
            manualDuration: {
                //code: formData.get("manualDuration-code"),
                value: JSON.parse(formData.get('manualDuration-value'))
            },
            minProd: {
                //code: formData.get("minProd-code"),
                value: JSON.parse(formData.get('minProd-value'))
            },
            packaging: {
                //code: formData.get("packaging-code"),
                value: JSON.parse(formData.get('packaging-value'))
            },
            packagingKind: formData.get('packagingKind'),
            productionDelay: {
                //code: formData.get("productionDelay-code"),
                value: JSON.parse(formData.get('productionDelay-value'))
            }
        }

        const item = generateProduct(value)
        await item.updateProduction(data)
        await fetchProductStore.fetchOne(idProduct)
    }
    async function updateLogistique(value) {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)

        const data = {
            customsCode: formData.get('customsCode'),
            incoterms: formData.get('incoterms'),
            minDelivery: {
                code: formData.get('minDelivery-code'),
                value: JSON.parse(formData.get('minDelivery-value'))
            },
            minStock: {
                code: formData.get('minStock-code'),
                value: JSON.parse(formData.get('minStock-value'))
            },

            weight: {
                code: formData.get('weight-code'),
                value: JSON.parse(formData.get('weight-value'))
            }
        }
        try {
            const item = generateProduct(value)
            await item.updateLogistique(data)
            await fetchProductStore.fetchOne(idProduct)
        } catch (error) {
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
    }
    async function updateFichiers(value) {
        const productsId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)

        const data = {
            category: formData.get('category'),
            file: formData.get('file'),
            product: `/api/products/${productsId}`
        }
        try {
            await fetchProductAttachmentStore.ajout(data)
            isError.value = false
        } catch (error) {
            const err = {
                message: error
            }
            violations.value.push(err)
            isError.value = true
        }
    }
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addGeneralites"
                :fields="Géneralitésfields"
                :component-attribute="fetchProductStore.product"
                @update="updateGeneral(fetchProductStore.product)"/>
        </AppTab>
        <AppTab
            id="gui-start-project"
            title="Project"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addProject"
                :fields="Projectfields"
                :component-attribute="fetchProductStore.product"
                @update="updateProject(fetchProductStore.product)"/>
            <AppFormJS
                v-if="managedCopperValue"
                id="manager"
                :fields="ProjectfieldsPrix"
                :model-value="fetchProductStore.product"
                disabled/>
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Production"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addProduction"
                :fields="Productionfields"
                :component-attribute="fetchProductStore.product"
                @update="updateProduction(fetchProductStore.product)"/>
        </AppTab>
        <!-- <AppTab
      id="gui-start-quality"
      title="Qualité"
      icon="certificate"
      tabs="gui-start"
    >
      <AppCardShow id="addQualite" />
    </AppTab> -->
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <AppCardShow
                id="addLogistique"
                :fields="Logistiquefields"
                :component-attribute="fetchProductStore.product"
                @update="updateLogistique(fetchProductStore.product)"/>
            <div v-if="isError2" class="alert alert-danger" role="alert">
                <div v-for="violation in violations2" :key="violation">
                    <li>{{ violation.propertyPath }} {{ violation.message }}</li>
                </div>
            </div>
        </AppTab>
        <AppTab
            id="gui-start-purchase-admin"
            title="Admin"
            icon="pallet"
            tabs="gui-start">
            <AppCardShow
                id="addAdmin"
                :fields="Adminfields"
                :component-attribute="fetchProductStore.product"
                @update="updateAdmin(fetchProductStore.product)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                @update="updateFichiers(fetchProductStore.product)"/>
            <div v-if="isError" class="alert alert-danger" role="alert">
                <div v-for="violation in violations" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
            <MyTree :node="treeData"/>
        </AppTab>
        <AppTab
            id="gui-start-Prix"
            title="Prix"
            icon="circle-dollar-to-slot"
            tabs="gui-start">
            <AppCardShow id="addPrice"/>
        </AppTab>
    <!-- <AppTab
      id="gui-start-addresses"
      title="Adresses\Contacts"
      icon="location-dot"
      tabs="gui-start"
    >
      <AppCardShow id="addAdresses" />
    </AppTab> -->
    </AppTabs>
</template>
