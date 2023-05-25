<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../components/MyTree.vue'
    import generateComponentAttribute from '../../../stores/component/componentAttribute'
    import {useColorsStore} from '../../../stores/colors/colors'
    import {useComponentAttachmentStore} from '../../../stores/component/componentAttachment'
    import {useComponentListStore} from '../../../stores/component/components'
    import {useComponentShowStore} from '../../../stores/component/componentAttributesList'
    import useOptions from '../../../stores/option/options'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component

    const emit = defineEmits(['update', 'update:modelValue'])

    const isError = ref(false)
    const isError2 = ref(false)
    const violations = ref([])
    const violations2 = ref([])
    const fecthOptions = useOptions('units')
    const fecthColors = useColorsStore()
    await fecthOptions.fetchOp()
    await fecthColors.fetch()

    const optionsAtt = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.value
            const optionList = {text, value}
            return optionList
        }))
    const optionsUnit = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.text
            const optionList = {text, value}
            return optionList
        }))
    // const optionsColors = computed(() =>
    //     fecthColors.colors.map(op => {
    //         const text = op.name
    //         const value = op.id
    //         const optionList = {text, value}
    //         return optionList
    //     }))

    const useFetchComponentStore = useComponentListStore()
    const useComponentStore = useComponentShowStore()
    const fetchComponentAttachment = useComponentAttachmentStore()
    await useComponentStore.fetchOne(idComponent)
    await fetchComponentAttachment.fetchOne(idComponent)
    await useFetchComponentStore.fetchOne(idComponent)
    const rohsValue = computed(() => useFetchComponentStore.component.rohs)
    const reachValue = computed(() => useFetchComponentStore.component.reach)
    useFetchComponentStore.component.price.code = 'EUR'

    const componentAttachment = computed(() =>
        fetchComponentAttachment.componentAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: componentAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${componentAttachment.value.length})`
        }
        return data
    })

    const attachmentByCategory = computed(() =>
        fetchComponentAttachment.componentAttachment.filter(
            attachment => attachment.category === 'rohs'
        ))
    const componentAttachmentByCategory = computed(() =>
        attachmentByCategory.value.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))

    const treeDataByRohs = computed(() => {
        const data = {
            children: componentAttachmentByCategory.value,
            icon: 'folder',
            id: 1,
            label: `Attachments Rohs (${componentAttachmentByCategory.value.length})`
        }
        return data
    })
    const attachmentByCategoryReach = computed(() =>
        fetchComponentAttachment.componentAttachment.filter(
            attachment => attachment.category === 'reach'
        ))
    const componentAttachmentByCategoryReach = computed(() =>
        attachmentByCategoryReach.value.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))

    const treeDataByReach = computed(() => {
        const data = {
            children: componentAttachmentByCategoryReach.value,
            icon: 'folder',
            id: 1,
            label: `Attachments Reach (${componentAttachmentByCategoryReach.value.length})`
        }
        return data
    })

    const Attributfields = [
        // {
        //     label: 'Couleur',
        //     name: 'getColor',
        //     options: {
        //         label: value =>
        //             optionsColors.value.find(option => option.type === value)?.text
        //             ?? null,
        //         options: optionsColors.value
        //     },
        //     type: 'select'
        // },
        {label: 'Attribute', name: 'attribute', type: 'text'},
        {label: 'Valeur', name: 'value', type: 'text'}
    // {label: 'Voltage (V)', name: 'Voltage', type: 'text'},
    // {label: 'Dia ext maxi (mm)', name: 'DiaMaxi', type: 'number'},
    // {label: 'Norme d\'appellation', name: 'Norme', type: 'text'},
    // {
    //     label: 'Nombre des conducteurs',
    //     name: 'NombreConducteurs',
    //     type: 'number'
    // },
    // {label: 'Section (mm²)', name: 'MatièreBrins', type: 'text'},
    // {label: 'T° mini (°C)', name: 'temperatureMini', type: 'number'},
    // {label: 'Matière d\'isolant', name: 'MatièreIsolant', type: 'text'},
    // {label: 'needJoint', name: 'needJoint', type: 'boolean'}
    ]
    const Fichiersfields = [
        {label: 'Categorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]
    const Qualitéfields = [
        {label: 'rohs ', name: 'rohs', type: 'boolean'},
        {label: 'rohsAttachment', name: 'rohsAttachment', type: 'file'},
        {label: 'reach', name: 'reach', type: 'boolean'},
        {label: 'reachAttachment', name: 'reachAttachment', type: 'file'},
        {label: 'Notation qualité *', name: 'quality', type: 'rating'}
    ]
    const Achatfields = [
        {label: 'Fabricant', name: 'manufacturer', type: 'text'},
        {label: 'Référence du Fabricant', name: 'manufacturerCode', type: 'text'}
    ]
    const Logistiquefields = [
        {label: 'Code douanier', name: 'customsCode', type: 'text'},
        {
            label: 'Poids',
            name: 'weight',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureselect'
        },
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value =>
                    optionsAtt.value.find(option => option.type === value)?.text ?? null,
                options: optionsAtt.value
            },
            type: 'select'
        },
        {
            label: 'Volume Prévisionnel',
            name: 'forecastVolume',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureselect'
        },
        {
            label: 'Stock Minimum',
            name: 'minStock',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureselect'
        },
        {label: 'gestionStock', name: 'managedStock', type: 'boolean'}
    ]

    const Spécificationfields = [
        {label: 'Prix', name: 'price', type: 'measure', measure: {code: 'Devise', value: 'valeur'}},
        {
            label: 'Poids Cuivre',
            name: 'copperWeight',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureselect'
        },
        {label: 'Info Cmde', name: 'orderInfo', type: 'text'}
    ]
    const Géneralitésfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Index', name: 'index', type: 'number'},
        {label: 'Note', name: 'notes', type: 'textarea'}
    ]

    async function update(value) {
        const form = document.getElementById('addAttribut')
        const formData = new FormData(form)
        const data = {
            color: `/api/colors/${formData.get('getColor')}`,
            measure: {
                code: 'U',
                value: 1
            },
            value: 'string'
        }
        const item = generateComponentAttribute(value)

        await item.updateAttributes(data)

        await useComponentStore.fetchOne(idComponent)
    }
    function updateLogistique() {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)
        const data = {
            customsCode: formData.get('customsCode'),
            family: '/api/component-families/4',
            forecastVolume: {
                code: formData.get('forecastVolume-code'),
                value: formData.get('forecastVolume-value')
            },
            managedStock: JSON.parse(formData.get('managedStock')),

            minStock: {
                code: formData.get('minStock-code'),
                value: formData.get('minStock-value')
            },
            orderInfo: formData.get('orderInfo'),
            unit: formData.get('unit'),
            weight: {
                code: formData.get('weight-code'),
                value: formData.get('weight-value')
            }
        }

        useFetchComponentStore.update(data, idComponent)
        useFetchComponentStore.fetchOne(idComponent)
    }
    function updateAchats() {
        const form = document.getElementById('addAchat')
        const formData = new FormData(form)
        const data = {
            manufacturer: formData.get('manufacturer'),
            manufacturerCode: formData.get('manufacturerCode')
        }
        useFetchComponentStore.updatePurchase(data, idComponent)
        useFetchComponentStore.fetchOne(idComponent)
    }
    const val = ref(Number(useFetchComponentStore.component.quality))
    async function input(value) {
        val.value = value.quality
        emit('update:modelValue', val.value)
        const data = {
            quality: val.value
        }
        await useFetchComponentStore.updateQuality(data, idComponent)
        await useFetchComponentStore.fetchOne(idComponent)
    }
    async function updateQuality() {
        const form = document.getElementById('addQualite')
        const formData = new FormData(form)
        const data = {
            //quality: JSON.parse(formData.get("quality")),
            reach: JSON.parse(formData.get('reach')),
            rohs: JSON.parse(formData.get('rohs'))
        }

        if (rohsValue.value) {
            const dataFichierRohs = {
                category: 'rohs',
                component: `/api/components/${idComponent}`,
                file: formData.get('rohsAttachment')
            }
            try {
                await fetchComponentAttachment.ajout(dataFichierRohs)
                await fetchComponentAttachment.fetchOne(idComponent)

                isError2.value = false
            } catch (error) {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
        if (reachValue.value) {
            const dataFichierReach = {
                category: 'reach',
                component: `/api/components/${idComponent}`,
                file: formData.get('reachAttachment')
            }
            try {
                await fetchComponentAttachment.ajout(dataFichierReach)
                await fetchComponentAttachment.fetchOne(idComponent)

                isError2.value = false
            } catch (error) {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
        await useFetchComponentStore.updateQuality(data, idComponent)
        await useFetchComponentStore.fetchOne(idComponent)
        rohsValue.value = computed(() => useFetchComponentStore.component.rohs)
        reachValue.value = computed(() => useFetchComponentStore.component.reach)
    }
    async function updateGeneral() {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        const data = {
            index: formData.get('index'),
            name: formData.get('name'),
            notes: formData.get('notes')
        }
        await useFetchComponentStore.updateAdmin(data, idComponent)
        await useFetchComponentStore.updateMain(data, idComponent)
        await useFetchComponentStore.fetchOne(idComponent)
    }
    async function updateSpecification() {
        const form = document.getElementById('addSpécification')
        const formData = new FormData(form)

        const data = {
            copperWeight: {
                code: formData.get('copperWeight-code'),
                value: formData.get('copperWeight-value')
            },
            orderInfo: formData.get('orderInfo')
        }
        // const dataWeight = {
        //   copperWeight: {
        //     code: formData.get("copperWeight-code"),
        //     value: formData.get("copperWeight-value"),
        //   },
        // };

        await useFetchComponentStore.updatePrice(data, idComponent)
        await useFetchComponentStore.fetchOne(idComponent)
        useFetchComponentStore.component.price.code = 'EUR'
    }
    async function updateFichiers() {
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)
        const data = {
            category: formData.get('category'),
            component: `/api/components/${idComponent}`,
            file: formData.get('file')
        }

        try {
            await fetchComponentAttachment.ajout(data)
            await fetchComponentAttachment.fetchOne(idComponent)

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
                :component-attribute="useFetchComponentStore.component"
                @update="updateGeneral"/>
        </AppTab>
        <AppTab
            id="gui-start-attribut"
            title="Attribut"
            icon="sitemap"
            tabs="gui-start">
            <AppCardShow
                v-for="item in useComponentStore.componentAttribute"
                id="addAttribut"
                :key="item.id"
                :fields="Attributfields"
                :component-attribute="item"
                @update="update(item)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                @update="updateFichiers(useFetchComponentStore.component)"/>
            <div v-if="isError" class="alert alert-danger" role="alert">
                <div v-for="violation in violations" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
            <MyTree :node="treeData"/>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <AppCardShow
                id="addQualite"
                :fields="Qualitéfields"
                :component-attribute="useFetchComponentStore.component"
                @update="updateQuality(useFetchComponentStore.component)"
                @update:model-value="input"/>
            <div v-if="isError2" class="alert alert-danger" role="alert">
                <div v-for="violation in violations2" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
            <MyTree v-show="rohsValue" :node="treeDataByRohs"/>
            <MyTree v-show="reachValue" :node="treeDataByReach"/>
        </AppTab>
        <AppTab
            id="gui-start-achat"
            title="Achat"
            icon="bag-shopping"
            tabs="gui-start">
            <AppCardShow
                id="addAchat"
                :fields="Achatfields"
                :component-attribute="useFetchComponentStore.component"
                @update="updateAchats(useFetchComponentStore.component)"/>
        </AppTab>
        <AppTab
            id="gui-start-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <AppCardShow
                id="addLogistique"
                :fields="Logistiquefields"
                :component-attribute="useFetchComponentStore.component"
                @update="updateLogistique(useFetchComponentStore.component)"/>
        </AppTab>
        <AppTab
            id="gui-start-spécifications"
            title="Spécification"
            icon="file-contract"
            tabs="gui-start">
            <AppCardShow
                id="addSpécification"
                :fields="Spécificationfields"
                :component-attribute="useFetchComponentStore.component"
                @update="updateSpecification(useFetchComponentStore.component)"/>
        </AppTab>
    </AppTabs>
</template>
