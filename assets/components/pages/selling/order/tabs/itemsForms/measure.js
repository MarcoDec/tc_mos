import api from "../../../../../../api";

class Measure {
    constructor(optionsUnit) {
        this.measure = 0;
        this.optionsUnit = optionsUnit;
    }

    add() {
        this.measure++;
    }

    get() {
        return this.measure;
    }

    setQuantityToMinDelivery(localData, objectWithMinDelivery) {
        // Lors de la sélection d'un produit nous en récupérons les informations de livraison minimale et nous les affectons aux quantités demandées et confirmées
        //console.info('Positionnement MinDelivery à ', objectWith.minDelivery)
        if (localData.requestedQuantity) {
            // En 1ère approximation, nous positionnons la quantité minimale uniquement lorsque sa valeur est supérieure à la valeur actuelle
            if (localData.requestedQuantity.value < objectWithMinDelivery.minDelivery.value) {
                localData.requestedQuantity.code = this.optionsUnit.value.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value
                localData.requestedQuantity.value = objectWithMinDelivery.minDelivery.value
            }
        } else {
            localData.requestedQuantity = {
                code: this.optionsUnit.value.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value,
                value: objectWithMinDelivery.minDelivery.value
            }
        }
        if (localData.confirmedQuantity) {
            // En 1ère approximation, nous positionnons la quantité minimale uniquement lorsque sa valeur est supérieure à la valeur actuelle
            if (localData.confirmedQuantity.value < objectWithMinDelivery.minDelivery.value) {
                localData.confirmedQuantity.code = this.optionsUnit.value.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value
                localData.confirmedQuantity.value = objectWithMinDelivery.minDelivery.value
            }
        } else localData.confirmedQuantity = {
            code: this.optionsUnit.value.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value,
            value: objectWithMinDelivery.minDelivery.value
        }
    }

    setQuantityToUnit(localData, response) {
        // Lors de la sélection d'un composant nous en récupérons les informations de l'unité associé (pas de champ minDelivery) et nous les affectons aux quantités demandées et confirmées
        if (localData.requestedQuantity) localData.requestedQuantity.code = response.unit
        else localData.requestedQuantity = {code: response.unit}
        if (localData.confirmedQuantity) localData.confirmedQuantity.code = response.unit
        else localData.confirmedQuantity = {code: response.unit}
    }

    async getProductGridPrice(product, customer, order, quantity) {
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

    async getComponentGridPrice(component, customer, order, quantity) {
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

    async getAndSetProductPrice(product, customer, order, quantity, localData, formKey) {
        await this.getProductGridPrice(product, customer, order, quantity).then(async price => {
            if (typeof price === 'string') window.alert(price)
            else {
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.value.price.value = price.value
                localData.value.price.code = currency['hydra:member'][0]['@id']
                formKey.value++
            }
        })
    }

    async getAndSetComponentPrice(component, customer, order, quantity, localData, formKey) {
        await this.getComponentGridPrice(component, customer, order, quantity).then(async price => {
            if (typeof price === 'string') window.alert(price)
            else {
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.value.price.value = price.value
                localData.value.price.code = currency['hydra:member'][0]['@id']
                formKey.value++
            }
        })
    }
}

export default Measure;