<script setup>
    import AppFormJS from '../../../../form/AppFormJS.js'
    import AppModal from '../../../../modal/AppModal.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import api from '../../../../../api'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../stores/option/options'
    import useUser from '../../../../../stores/security'
    import {Modal} from 'bootstrap'
    import {computed, ref} from 'vue'
    import {useCustomerOrderItemsStore} from '../../../../../stores/customer/customerOrderItems'

    const props = defineProps({
        order: {default: () => ({}), required: true, type: Object},
        customer: {default: () => ({}), required: true, type: Object}
    })
    //console.log('customer', props.customer)
    //region initialisation des constantes et variables
    const fetchUser = useUser()
    const isSellingWriterOrAdmin = fetchUser.isSellingWriter || fetchUser.isSellingAdmin
    const roleUser = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')

    const fetchUnitOptions = useOptions('units')
    const fetchCurrencyOptions = useOptions('currencies')
    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    const customerOrderItemsCriteria = useFetchCriteria('customer-order-items-criteria')
    const tableKey = ref(0)

    const customerOrderItemForecastCreateModal = ref(null)
    const localForecastData = ref({
        product: null,
        component: null,
        requestedQuantity: {
            code: null,
            value: null
        },
        requestedDate: null,
        price: {
            code: null,
            value: null
        }})
    const forecastFormKey = ref(0)

    const customerOrderItemFixedCreateModal = ref(null)
    const localFixedData = ref({
        product: null,
        component: null,
        requestedQuantity: {
            code: null,
            value: null
        },
        confirmedQuantity: {
            code: null,
            value: null
        },
        requestedDate: null,
        confirmedDate: null,
        price: {
            code: null,
            value: null
        }
    })
    const fixedFormKey = ref(0)

    const stateOptions = [
        {text: 'partially_delivered', value: 'partially_delivered'},
        {text: 'delivered', value: 'delivered'},
        {text: 'agreed', value: 'agreed'}
    ]
    const fixedFamilies = ['fixed', 'edi_orders', 'free']
    //region      initialisation des données computed
    const fieldsOrderItem = computed(() => [
        {
            label: 'Produit',
            name: 'product',
            info: 'Si un produit est sélectionné, la quantité minimale de livraison définie sur la fiche produit sera affectée aux quantités demandées et confirmées.',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            max: 1},
        {
            label: 'Composant',
            name: 'component',
            info: 'Si un composant est sélectionné, l\'unité associée sera affectée aux quantités demandées et confirmées.',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Quantité souhaitée *',
            name: 'requestedQuantity',
            info: 'Obligatoire\nSi un produit est sélectionnée, cette quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit.\n' +
                'Lorsque la quantité change le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'requestedQuantity.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'requestedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Quantité confirmée',
            name: 'confirmedQuantity',
            info: 'Si un produit est sélectionnée, cette quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit.\n' +
                'Lorsque la quantité change le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'confirmedQuantity.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'confirmedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Date de livraison souhaitée *',
            name: 'requestedDate',
            info: 'Obligatoire',
            type: 'date'
        },
        {
            label: 'Date de livraison confirmée',
            name: 'confirmedDate',
            type: 'date'
        },
        {
            label: 'Prix Unitaire',
            name: 'price',
            type: 'measure',
            info: 'Le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client.\n' +
                'Il est calculé en fonction de la quantité confirmée si la quantité est non nulle, sinon il est calculé à partir de la quantité souhaitée.\n' +
                'La valeur du prix unitaire peut être modifiée manuellement.',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value =>
                            optionsCurrency.value.find(option => option.type === value)?.text ?? null,
                        options: optionsCurrency.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'price.value',
                    type: 'number',
                    step: 0.0001
                }
            }
        }
    ])
    const fieldsOpenOrderItem = computed(() => [
        {
            label: 'Produit',
            name: 'product',
            info: 'Si un produit est sélectionné, la quantité minimale de livraison définie sur la fiche produit sera affectée aux quantités demandées.',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Composant',
            name: 'component',
            info: 'Si un composant est sélectionné, l\'unité associée sera affectée aux quantités demandées et confirmées.',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
            info: 'Obligatoire\nSi un produit est sélectionnée, cette quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit.\n' +
                'Lorsque la quantité change le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client',
            measure: {
                code: {
                    label: 'Code',
                    name: 'requestedQuantity.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'requestedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Date de livraison souhaitée',
            name: 'requestedDate',
            type: 'date'
        },
        {
            label: 'Prix Unitaire',
            name: 'price',
            type: 'measure',
            info: 'Le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client.\n' +
                'Il est calculé en fonction de la quantité souhaitée.\n' +
                'La valeur du prix unitaire peut être modifiée manuellement.',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value =>
                            optionsCurrency.value.find(option => option.type === value)?.text ?? null,
                        options: optionsCurrency.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'price.value',
                    type: 'number',
                    step: 0.0001
                }
            }
        }
    ])
    const customerOrderItems = computed(() => storeCustomerOrderItems.itemsCustomerOrders)
    const optionsUnit = computed(() =>
        fetchUnitOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const optionsCurrency = computed(() =>
        fetchCurrencyOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const fieldsCommande = computed(() => [
        {label: 'Forecast', name: 'isForecast', type: 'boolean', width: 50},
        {label: 'Produit', name: 'product', type: 'multiselect-fetch', api: '/api/products', filteredProperty: 'code', max: 1},
        {label: 'Composant', name: 'component', type: 'multiselect-fetch', api: '/api/components', filteredProperty: 'code', max: 1},
        {
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
            info: 'La quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'requestedQuantity.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'requestedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure',
            width: 150
        },
        {
            label: 'Quantité confirmée',
            name: 'confirmedQuantity',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'confirmedQuantity.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'confirmedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure',
            width: 150
        },
        {label: 'date de livraison souhaitée', name: 'requestedDate', trie: true, type: 'date', width: 80},
        {label: 'Date de livraison confirmée', name: 'confirmedDate', trie: true, type: 'date', width: 80},
        {
            label: 'Prix Unitaire',
            name: 'price',
            trie: false,
            type: 'measure',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value =>
                            optionsCurrency.value.find(option => option.type === value)?.text ?? null,
                        options: optionsCurrency.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'price.value',
                    type: 'number',
                    step: 0.01
                }
            }
        },
        {
            label: 'Etat',
            name: 'state',
            options: {
                label: value =>
                    stateOptions.find(option => option.type === value)?.text ?? null,
                options: stateOptions
            },
            trie: true,
            type: 'select'
        },
        {label: 'Référence item de commande', name: 'ref', trie: true, type: 'text'},
        {label: 'Description', name: 'notes', trie: true, type: 'text'}
    ])
    //endregion
    //endregion

    //region Methods
    /**
     * Cette fonction permet de récupérer le prix unitaire d'un produit en fonction de:
     * -> la quantité demandée
     * -> le client associé à la commande
     * -> le produit associé à la commande
     * -> le type de produit associé à la commande
     * @param product
     * @param customer
     * @param order
     * @param quantity
     * @returns {Promise<{code: null, value: null}|{code: null, value: null}|*|null>}
     */
    async function getProductGridPrice(product, customer, order, quantity) {
        // on récupère le type de produit associé à la commande
        const kind = order.kind
        // on récupère les iri produit et client
        const productIri = product['@id']
        const customerIri = customer['@id']
        // on récupère le productCustomer associé au produit et au client
        const productCustomer = await api(`/api/customer-products?product=${productIri}&customer=${customerIri}&kind=${kind}`, 'GET')
        // Si il y a au moins une réponse on récupère le premier élément
        if (productCustomer['hydra:member'].length > 0) {
            // console.log('productCustomer', productCustomer['hydra:member'])
            // Normalement s'il existe plus d'une grilles tarifaire il faut alerter l'utilisateur
            if (productCustomer['hydra:member'].length > 1) {
                return `Il existe plus d'une grille tarifaire pour ce produit et ce client de type ${kind}`
            }
            const productCustomerItem = productCustomer['hydra:member'][0]
            // On récupère la grille tarifaire dans CustomerProductPrice associée au productCustomer
            const productCustomerPrices = await api(`/api/customer-product-prices?product=${productCustomerItem['@id']}`, 'GET')
            // console.log('productCustomerPrices', productCustomerPrices['hydra:member'])
            // Si il y a au moins une réponse on parcourt les éléments pour récupérer les prix dont la quantité associée est supérieure ou égale à la quantité demandée
            if (productCustomerPrices['hydra:member'].length > 0) {
                // On tri les éléments par ordre décroissant de quantité
                productCustomerPrices['hydra:member'].sort((a, b) => b.quantity.value - a.quantity.value)
                // on récupère le premier élément dont la quantité est supérieure ou égale à la quantité demandée
                const productCustomerPricesItems = productCustomerPrices['hydra:member'].find(price => {
                    return price.quantity.value <= quantity.value
                })
                // console.log('productCustomerPricesItems 1er élément avec quantité', productCustomerPricesItems)
                return productCustomerPricesItems.price
            }
            return 'Il n\'y a pas de grille de prix pour ce produit et ce client'
        } else {
            return 'Ce produit n\'est pas associé à ce client'
        }
    }
    async function getComponentGridPrice(component, customer, order, quantity) {
        // on récupère le type de produit associé à la commande
        const kind = order.kind
        // on récupère les iri produit et client
        const componentIri = component['@id']
        const customerIri = customer['@id']
        // on récupère le productCustomer associé au produit et au client
        const componentCustomer = await api(`/api/customer-components?component=${componentIri}&customer=${customerIri}&kind=${kind}`, 'GET')
        // Si il y a au moins une réponse on récupère le premier élément
        if (componentCustomer['hydra:member'].length > 0) {
            // console.log('componentCustomer', componentCustomer['hydra:member'])
            // Normalement s'il existe plus d'une grilles tarifaire il faut alerter l'utilisateur
            if (componentCustomer['hydra:member'].length > 1) {
                return `Il existe plus d'une grille tarifaire pour ce composant et ce client de type ${kind}`
            }
            const componentCustomerItem = componentCustomer['hydra:member'][0]
            // On récupère la grille tarifaire dans CustomerProductPrice associée au productCustomer
            const componentCustomerPrices = await api(`/api/customer-component-prices?component=${componentCustomerItem['@id']}`, 'GET')
            // console.log('componentCustomerPrices', componentCustomerPrices['hydra:member'])
            // Si il y a au moins une réponse on parcourt les éléments pour récupérer les prix dont la quantité associée est supérieure ou égale à la quantité demandée
            if (componentCustomerPrices['hydra:member'].length > 0) {
                // On tri les éléments par ordre décroissant de quantité
                componentCustomerPrices['hydra:member'].sort((a, b) => b.quantity.value - a.quantity.value)
                // on récupère le premier élément dont la quantité est supérieure ou égale à la quantité demandée
                const componentCustomerPricesItems = componentCustomerPrices['hydra:member'].find(price => {
                    return price.quantity.value <= quantity.value
                })
                // console.log('componentCustomerPricesItems 1er élément avec quantité', componentCustomerPricesItems)
                return componentCustomer
            }
            return 'Il n\'y a pas de grille de prix pour ce composant et ce client'
        } else {
            return 'Ce composant n\'est pas associé à ce client'
        }
    }
    function setQuantityToMinDelivery(localData, response) {
        // Lors de la sélection d'un produit nous en récupérons les informations de livraison minimale et nous les affectons aux quantités demandées et confirmées
        //console.info('Positionnement MinDelivery à ', response.minDelivery)
        if (localData.requestedQuantity) {
            // En 1ère approximation, nous positionnons la quantité minimale uniquement lorsque sa valeur est supérieure à la valeur actuelle
            if (localData.requestedQuantity.value < response.minDelivery.value) {
                localData.requestedQuantity.code = optionsUnit.value.find(unit => unit.text === response.minDelivery.code).value
                localData.requestedQuantity.value = response.minDelivery.value
            }
        }
        else {
            localData.requestedQuantity = {
                code: optionsUnit.value.find(unit => unit.text === response.minDelivery.code).value,
                value: response.minDelivery.value
            }
        }
        if (localData.confirmedQuantity) {
            // En 1ère approximation, nous positionnons la quantité minimale uniquement lorsque sa valeur est supérieure à la valeur actuelle
            if (localData.confirmedQuantity.value < response.minDelivery.value) {
                localData.confirmedQuantity.code = optionsUnit.value.find(unit => unit.text === response.minDelivery.code).value
                localData.confirmedQuantity.value = response.minDelivery.value
            }
        }
        else localData.confirmedQuantity = {
            code: optionsUnit.value.find(unit => unit.text === response.minDelivery.code).value,
            value: response.minDelivery.value
        }
    }
    function setQuantityToUnit(localData, response) {
        // Lors de la sélection d'un composant nous en récupérons les informations de l'unité associé (pas de champ minDelivery) et nous les affectons aux quantités demandées et confirmées
        if (localData.requestedQuantity) localData.requestedQuantity.code = response.unit
        else localData.requestedQuantity = {code: response.unit}
        if (localData.confirmedQuantity) localData.confirmedQuantity.code = response.unit
        else localData.confirmedQuantity = {code: response.unit}
    }
    async function getAndSetProductPrice(product, customer, order, quantity, localData, formKey) {
        await getProductGridPrice(product, customer, order, quantity).then(async price => {
            console.log('price', price)
            if (typeof price === 'string') window.alert(price)
            else {
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.value.price.value = price.value
                localData.value.price.code = currency['hydra:member'][0]['@id']
                formKey.value++
            }
        })
    }
    async function getAndSetComponentPrice(component, customer, order, quantity, localData, formKey) {
        await getComponentGridPrice(component, customer, order, quantity).then(async price => {
            if (typeof price === 'string') window.alert(price)
            else {
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.value.price.value = price.value
                localData.value.price.code = currency['hydra:member'][0]['@id']
                formKey.value++
            }
        })
    }
    async function updateFixedValue(value) {
        const initialLocalData = localFixedData.value
        localFixedData.value = value

        if (value.product && value.product !== initialLocalData.product) {
            localFixedData.value.component = null
            await api(value.product, 'GET').then(async response => {
                setQuantityToMinDelivery(localFixedData.value, response)
                await getAndSetProductPrice(response, props.customer, props.order, localFixedData.value.requestedQuantity, localFixedData, fixedFormKey)
            })
            return
        }
        if (value.component && value.component !== initialLocalData.component) {
            localFixedData.value.product = null
            await api(value.component, 'GET').then(async response => {
                setQuantityToUnit(localFixedData.value, response)
                await getAndSetComponentPrice(response, props.customer, props.order, localFixedData.value.requestedQuantity, localFixedData, fixedFormKey)
            })
            return
        }
        if (value.confirmedQuantity.value && value.confirmedQuantity.value !== initialLocalData.confirmedQuantity.value) {
            // Si un produit a été sélectionné on récupère le prix unitaire associé au produit et au client
            if (value.product) {
                await getAndSetProductPrice(value.product, props.customer, props.order, localFixedData.value.confirmedQuantity, localFixedData, fixedFormKey)
                return
            }
            if (value.component) {
                await getAndSetComponentPrice(value.component, props.customer, props.order, localFixedData.value.confirmedQuantity, localFixedData, fixedFormKey)
                return
            }
        }
        if (value.requestedQuantity.value && value.requestedQuantity.value !== initialLocalData.requestedQuantity.value) {
            // Si un produit a été sélectionné on récupère le prix unitaire associé au produit et au client
            if (value.product) {
                await getAndSetProductPrice(value.product, props.customer, props.order, localFixedData.value.requestedQuantity, localFixedData, fixedFormKey)
                return
            }
            if (value.component) {
                await getAndSetComponentPrice(value.component, props.customer, props.order, localFixedData.value.requestedQuantity, localFixedData, fixedFormKey)
            }
        }
    }
    async function updateForecastValue(value) {
        const initialLocalData = localForecastData.value
        localForecastData.value = value

        if (value.product && value.product !== initialLocalData.product) {
            localForecastData.value.component = null
            await api(value.product, 'GET').then(async response => {
                setQuantityToMinDelivery(localForecastData.value, response)
                await getAndSetProductPrice(response, props.customer, props.order, localForecastData.value.requestedQuantity, localForecastData, forecastFormKey)
            })
            return
        }
        if (value.component && value.component !== initialLocalData.component) {
            localForecastData.value.product = null
            await api(value.component, 'GET').then(async response => {
                setQuantityToUnit(localForecastData.value, response)
                await getAndSetComponentPrice(response, props.customer, props.order, localForecastData.value.requestedQuantity, localForecastData, forecastFormKey)
            })
            return
        }
        if (value.requestedQuantity.value && value.requestedQuantity.value !== initialLocalData.requestedQuantity.value) {
            // Si un produit a été sélectionné on récupère le prix unitaire associé au produit et au client
            if (value.product) {
                await getAndSetProductPrice(value.product, props.customer, props.order, localForecastData.value.requestedQuantity, localForecastData, forecastFormKey)
                return
            }
            if (value.component) {
                await getAndSetComponentPrice(value.component, props.customer, props.order, localForecastData.value.requestedQuantity, localForecastData, forecastFormKey)
            }
        }
    }
    async function refreshTableCustomerOrders() {
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    function addFixedItem() {
        //On ajoute le champ parentOrder
        localFixedData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast à false
        localFixedData.value.isForecast = false
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        const requestedUnit = optionsUnit.value.find(unit => unit.value === localFixedData.value.requestedQuantity.code)
        const confirmedUnit = optionsUnit.value.find(unit => unit.value === localFixedData.value.confirmedQuantity.code)
        //Si c'est un produit on positionne la clé item avec la valeur de la clé produit, sinon on positionne la clé item avec la valeur de la clé component
        if (localFixedData.value.product) localFixedData.value.item = localFixedData.value.product
        else localFixedData.value.item = localFixedData.value.component
        localFixedData.value.requestedQuantity.code = requestedUnit.text
        localFixedData.value.confirmedQuantity.code = confirmedUnit.text
        storeCustomerOrderItems.add(localFixedData.value)
        //On ferme la modale
        if (customerOrderItemFixedCreateModal.value) {
            const modalElement = customerOrderItemFixedCreateModal.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
        }
        //On réinitalise les données locales
        localFixedData.value = {}
        refreshTableCustomerOrders()
        tableKey.value++
    }
    function addForecastItem() {
        //On ajoute le champ parentOrder
        localForecastData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast
        localForecastData.value.isForecast = true
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        const requestedUnit = optionsUnit.value.find(unit => unit.value === localForecastData.value.requestedQuantity.code)
        //Si c'est un produit on positionne la clé item avec la valeur de la clé produit, sinon on positionne la clé item avec la valeur de la clé component
        if (localForecastData.value.product) localForecastData.value.item = localForecastData.value.product
        else localForecastData.value.item = localForecastData.value.component
        localForecastData.value.requestedQuantity.code = requestedUnit.text
        storeCustomerOrderItems.add(localForecastData.value)
        //On ferme la modale
        if (customerOrderItemForecastCreateModal.value) {
            const modalElement = customerOrderItemForecastCreateModal.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
        }
        //On réinitalise les données locales
        localForecastData.value = {}
        //On rafraichit les données du tableau
        refreshTableCustomerOrders()
        //On rafraichit le formulaire
        tableKey.value++
    }
    async function deletedCustomerOrderItem(idRemove) {
        await storeCustomerOrderItems.remove(idRemove)
        await refreshTableCustomerOrders()
    }
    async function getPageCustomerOrders(nPage) {
        customerOrderItemsCriteria.gotoPage(parseFloat(nPage))
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    function addPermanentFilters() {
        customerOrderItemsCriteria.addFilter('parentOrder', props.order['@id'])
    }
    async function searchCustomerOrders(inputValues) {
        // console.log('inputValues', inputValues)
        customerOrderItemsCriteria.resetAllFilter()
        addPermanentFilters()
        if (inputValues.product) customerOrderItemsCriteria.addFilter('item', inputValues.product)
        if (inputValues.component) customerOrderItemsCriteria.addFilter('item', inputValues.component)
        if (inputValues.ref) customerOrderItemsCriteria.addFilter('ref', inputValues.ref)
        if (inputValues.requestedQuantity) customerOrderItemsCriteria.addFilter('requestedQuantity.value', inputValues.requestedQuantity.value)
        if (inputValues.requestedQuantity) {
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.requestedQuantity.code)
            customerOrderItemsCriteria.addFilter('requestedQuantity.code', requestedUnit.code)
        }
        if (inputValues.requestedDate) customerOrderItemsCriteria.addFilter('requestedDate', inputValues.requestedDate)
        if (inputValues.confirmedQuantity) customerOrderItemsCriteria.addFilter('confirmedQuantity.value', inputValues.confirmedQuantity.value)
        if (inputValues.confirmedQuantity) {
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.confirmedQuantity.code)
            customerOrderItemsCriteria.addFilter('confirmedQuantity.code', requestedUnit.code)
        }
        if (inputValues.confirmedDate) customerOrderItemsCriteria.addFilter('confirmedDate', inputValues.confirmedDate)
        if (inputValues.state) customerOrderItemsCriteria.addFilter('embState.state', inputValues.state)
        if (inputValues.notes) customerOrderItemsCriteria.addFilter('notes', inputValues.notes)
        if (inputValues.product && !inputValues.component) {
            await storeCustomerOrderItems.fetchAllProduct(customerOrderItemsCriteria.getFetchCriteria)
            return
        }
        if (!inputValues.product && inputValues.component) {
            await storeCustomerOrderItems.fetchAllComponent(customerOrderItemsCriteria.getFetchCriteria)
            return
        }
        if (inputValues.product && inputValues.component) {
            window.alert('Vous ne pouvez pas rechercher à la fois un produit et un composant')
            return
        }
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    async function cancelSearchCustomerOrders() {
        customerOrderItemsCriteria.resetAllFilter()
        addPermanentFilters()
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
        //On réinitialise les données du formulaire
        document.getElementById('formCustomerOrdersTable').reset()
    }
    async function trierAlphabetCustomerOrders(payload) {
        addPermanentFilters()
        if (payload.name === 'requestedQuantity') {
            customerOrderItemsCriteria.addSort('requestedQuantity.value', payload.direction)
        } else if (payload.name === 'confirmedQuantity') {
            customerOrderItemsCriteria.addSort('confirmedQuantity.value', payload.direction)
        } else if (payload.name === 'state') {
            customerOrderItemsCriteria.addSort('embState.state', payload.direction)
        } else {
            customerOrderItemsCriteria.addSort(payload.name, payload.direction)
        }
        // console.log('customerOrderItemsCriteria', customerOrderItemsCriteria.getFetchCriteria)
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    //endregion
    //chargement des données
    addPermanentFilters()
    const promises = []
    promises.push(fetchUnitOptions.fetchOp())
    promises.push(fetchCurrencyOptions.fetchOp())
    promises.push(storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria))
    await Promise.all(promises)
    //endregion
</script>

<template>
    <AppSuspense>
        <AppModal
            id="modalAddNewOrderItem"
            ref="customerOrderItemFixedCreateModal"
            title="Ajouter Item en Ferme">
            <AppFormJS
                id="formAddNewOrderItem"
                :key="fixedFormKey"
                :model-value="localFixedData"
                :fields="fieldsOrderItem"
                submit-label="Ajouter"
                @update:model-value="updateFixedValue"
                @submit="addFixedItem"/>
        </AppModal>
        <AppModal
            v-if="!fixedFamilies.includes(order.orderFamily)"
            id="modalAddNewForecastItem"
            ref="customerOrderItemForecastCreateModal"
            title="Ajouter Item en Prévisionnel">
            <AppFormJS
                id="formAddNewOrderItem"
                :key="forecastFormKey"
                :model-value="localForecastData"
                :fields="fieldsOpenOrderItem"
                submit-label="Ajouter"
                @update:model-value="updateForecastValue"
                @submit="addForecastItem"/>
        </AppModal>
        <AppCardableTable
            :key="tableKey"
            :current-page="storeCustomerOrderItems.currentPage"
            :fields="fieldsCommande"
            :first-page="storeCustomerOrderItems.firstPage"
            :items="customerOrderItems"
            :last-page="storeCustomerOrderItems.lastPage"
            :next-page="storeCustomerOrderItems.nextPage"
            :pag="storeCustomerOrderItems.pagination"
            :previous-page="storeCustomerOrderItems.previousPage"
            :user="roleUser"
            title
            form="formCustomerOrdersTable"
            @deleted="deletedCustomerOrderItem"
            @get-page="getPageCustomerOrders"
            @trier-alphabet="trierAlphabetCustomerOrders"
            @search="searchCustomerOrders"
            @cancel-search="cancelSearchCustomerOrders">
            <template #title>
                <span>Items de commande {{ order.ref }}</span>
                <button
                    class="btn btn-success btn-float-right m-1"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAddNewOrderItem">
                    Ajouter Item en Ferme
                </button>
                <button
                    v-if="!fixedFamilies.includes(order.orderFamily)"
                    class="btn btn-success btn-float-right m-1"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAddNewForecastItem">
                    Ajouter Item en Prévisionnel {{ order.orderFamily }}
                </button>
            </template>
        </AppCardableTable>
    </AppSuspense>
</template>
