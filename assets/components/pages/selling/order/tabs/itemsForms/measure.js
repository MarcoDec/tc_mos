import api from '../../../../../../api'
import Unit from './unit'

class Measure {
    //region méthodes statiques
    static async getOptionsUnit() {
        return (await api('/api/units', 'GET'))['hydra:member']
    }

    static async getOptionsCurrency() {
        return (await api('/api/currencies', 'GET'))['hydra:member']
    }

    static async getUnitByCode(code) {
        return api(`/api/units?code=${code}`, 'GET')
    }

    static async getCurrencyByCode(code) {
        return api(`/api/currencies?code=${code}`, 'GET')
    }

    static setQuantityToUnit(localData, response) {
        // Lors de la sélection d'un composant nous en récupérons les informations de l'unité associé (pas de champ minDelivery) et nous les affectons aux quantités demandées et confirmées
        if (localData.requestedQuantity) localData.requestedQuantity.code = response.unit
        else localData.requestedQuantity = {code: response.unit}
        if (localData.confirmedQuantity) localData.confirmedQuantity.code = response.unit
        else localData.confirmedQuantity = {code: response.unit}
    }

    static async getAndSetProductPrice(product, customer, order, quantity, localData, formKey) {
        await Measure.getProductGridPrice(product, customer, order, quantity).then(async price => {
            if (typeof price === 'string') window.alert(price)
            else {
                if (price === null) {
                    // console.log('pas de tarif trouvé')
                    return
                }
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.price.value = price.value
                localData.price.code = currency['hydra:member'][0]['@id']
                formKey.value++
            }
        })
    }

    static async getAndSetComponentPrice(component, customer, order, quantity, localData, formKey) {
        await Measure.getComponentGridPrice(component, customer, order, quantity).then(async price => {
            if (typeof price === 'string') window.alert(price)
            else {
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.price.value = price.value
                localData.price.code = currency['hydra:member'][0]['@id']
                formKey.value++
            }
        })
    }

    static async getComponentGridPrice(component, customer, order, quantity) {
        // on récupère le type de produit associé à la commande
        const kind = order.kind
        // on récupère les iri produit et client
        const componentIri = component['@id']
        const customerIri = customer['@id']
        // on récupère le productCustomer associé au produit et au client
        const componentCustomer = await api(`/api/customer-components?component=${componentIri}&customer=${customerIri}&kind=${kind}`, 'GET')
        // Si il y a au moins une réponse on récupère le premier élément
        if (componentCustomer['hydra:member'].length > 0) {
            // Normalement s'il existe plus d'une grilles tarifaire il faut alerter l'utilisateur
            if (componentCustomer['hydra:member'].length > 1) {
                return `Il existe plus d'une grille tarifaire pour ce composant et ce client de type ${kind}`
            }
            const componentCustomerItem = componentCustomer['hydra:member'][0]
            // On récupère la grille tarifaire dans CustomerProductPrice associée au productCustomer
            const componentCustomerPrices = await api(`/api/customer-component-prices?component=${componentCustomerItem['@id']}`, 'GET')
            // Si il y a au moins une réponse on parcourt les éléments pour récupérer les prix dont la quantité associée est supérieure ou égale à la quantité demandée
            if (componentCustomerPrices['hydra:member'].length > 0) {
                // On tri les éléments par ordre décroissant de quantité
                componentCustomerPrices['hydra:member'].sort((a, b) => b.quantity.value - a.quantity.value)
                // on récupère le premier élément dont la quantité est supérieure ou égale à la quantité demandée
                return componentCustomerPrices['hydra:member'].find(price => price.quantity.value <= quantity.value)
            }
            return 'Il n\'y a pas de grille de prix pour ce composant et ce client'
        }
        return 'Ce composant n\'est pas associé à ce client'
    }

    static async getProductGridPrice(product, customer, order, quantity) {
        const productIri = product['@id']
        // on récupère le type de produit associé à la commande
        const kind = order.kind
        const customerIri = customer['@id']
        // on récupère le productCustomer associé au produit et au client
        const productCustomer = await api(`/api/customer-products?product=${productIri}&customer=${customerIri}&kind=${kind}`, 'GET')
        // Si il y a au moins une réponse on récupère le premier élément
        if (productCustomer['hydra:member'].length > 0) {
            // Normalement s'il existe plus d'une grilles tarifaire il faut alerter l'utilisateur
            if (productCustomer['hydra:member'].length > 1) {
                return `Il existe plus d'une grille tarifaire pour ce produit ${productIri} et ce client ${customerIri} de type ${kind}`
            }
            const productCustomerItem = productCustomer['hydra:member'][0]
            // On récupère la grille tarifaire dans CustomerProductPrice associée au productCustomer
            const productCustomerPrices = await api(`/api/customer-product-prices?product=${productCustomerItem['@id']}`, 'GET')
            // Si il y a au moins une réponse on parcourt les éléments pour récupérer les prix dont la quantité associée est supérieure ou égale à la quantité demandée
            if (productCustomerPrices['hydra:member'].length > 0) {
                // On tri les éléments par ordre décroissant de quantité
                productCustomerPrices['hydra:member'].sort((a, b) => b.quantity.value - a.quantity.value)
                // on récupère le premier élément dont la quantité est supérieure ou égale à la quantité demandée
                const quantityMoreThan1 = quantity.value >= 1 ? quantity.value : 1
                const productCustomerPricesItems = productCustomerPrices['hydra:member'].find(price => price.quantity.value <= quantityMoreThan1)
                if (typeof productCustomerPricesItems === 'undefined') return null
                return productCustomerPricesItems.price
            }
            return 'Il n\'y a pas de grille de prix pour ce produit et ce client'
        }
        return 'Ce produit n\'est pas associé à ce client'
    }
    //endregion

    constructor(code, value, denominator = null, denominatorUnit = null) {
        // console.log('constructor Measure', code, value, denominator, denominatorUnit)
        this.code = code
        this.value = value
        this.denominator = denominator
        this.denominatorUnit = denominatorUnit
        if (code === null || code === '') {
            throw new Error('une unité de mesure doit être définie')
        }
        if (value === null) {
            this.value = 0.0
        }
    }

    async initUnits() {
        this.type = 'unit'
        const unitData = await Measure.getUnitByCode(this.code)
        this.unit = new Unit(unitData.code, unitData.name, unitData.parent, unitData.base)
        if (this.denominator !== null) {
            const denominatorUnitData = await Measure.getUnitByCode(this.denominator)
            this.denominatorUnit = new Unit(denominatorUnitData.code, denominatorUnitData.name, denominatorUnitData.parent, denominatorUnitData.base)
        }
    }

    async initCurrencies() {
        this.type = 'currency'
        const unitData = await Measure.getCurrencyByCode(this.code)
        this.unit = new Unit(unitData.code, unitData.name, unitData.parent, unitData.base)
        this.denominator = null
        this.denominatorUnit = null
    }

    //region getters et setters
    getCode() {
        return this.code
    }

    setCode(code) {
        this.code = code
        return this
    }

    async getSafeUnit() {
        if (this.unit === null) {
            //on récupère l'unité ayant le code this.code
            const unitData = await Measure.getUnitByCode(this.code)
            this.unit = new Unit(unitData.code, unitData.name, unitData.parent, unitData.base)
        }
        return this.unit
    }

    getValue() {
        return this.value
    }

    setValue(value) {
        this.value = value
        return this
    }

    getUnit() {
        return this.unit
    }

    setUnit(unit) {
        this.unit = unit
        return this
    }

    getDenominator() {
        return this.denominator
    }

    setDenominator(denominator) {
        this.denominator = denominator
        return this
    }

    getDenominatorUnit() {
        return this.denominatorUnit
    }

    setDenominatorUnit(denominatorUnit) {
        this.denominatorUnit = denominatorUnit
        return this
    }
    //endregion

    //region méthodes de conversion et calculs
    async isGreaterThanOrEqual(measure) {
        // console.log('isGreaterThanOrEqual', this.code)
        const clone = new Measure(this.code, this.value, this.denominator, this.denominatorUnit)
        // measure = clone.convertToSame(measure)
        await clone.convertToSame(measure)
        return clone.value >= measure.value
    }

    async add(measure) {
        if (measure.code === null || measure.code === '') throw new Error('add(measure) une unité de mesure doit être définie')
        switch (this.type) {
        case 'unit':
            if (measure.type !== 'unit') throw new Error('add(measure) les deux mesures doivent être de type unité')
            // eslint-disable-next-line require-atomic-updates
            measure.unit = measure.unit ?? await Measure.getUnitByCode(measure.code)
            break
        case 'currency':
            if (measure.type !== 'currency') throw new Error('add(measure) les deux mesures doivent être de type devise')
            // eslint-disable-next-line require-atomic-updates
            measure.unit = measure.unit ?? await Measure.getCurrencyByCode(measure.code)
            break
        default:
            throw new Error('add(measure) le type de mesure n\'est pas défini')
        }
        if (!measure.unit) throw new Error(`add(measure) l'unité de mesure ${measure.code} n'a pas été trouvée pour le type ${this.type}`)
        if (this.unit === null && this.code === null) {
            this.unit = measure.unit
            this.code = measure.code
            this.value = measure.value
            return this
        }
        if (this.unit === null) {
            this.unit = await Measure.getUnitByCode(this.code)
        }
        // measure = this.convertToSame(measure)
        await this.convertToSame(measure)
        this.value += measure.value
        return this
    }

    substract(measure) {
        return this.add(measure.setValue(-measure.value))
    }

    convert(unit, denominator = null) {
        const safeUnit = this.getSafeUnit()
        safeUnit.assertSameAs(unit)
        if (safeUnit.getCode() !== unit.getCode()) {
            this.value *= safeUnit.getConvertorDistance(unit)
            this.code = unit.getCode()
            this.unit = unit
        }

        if (denominator !== null) {
            if (this.denominator === null) {
                throw new Error('No denominator.')
            }
            if (this.denominatorUnit === null) {
                throw new Error('Unit not loaded.')
            }
            this.denominatorUnit.assertSameAs(denominator)
            if (this.denominatorUnit.getCode() !== denominator.getCode()) {
                this.value *= 1 / this.denominatorUnit.getConvertorDistance(denominator)
                this.denominator = denominator.getCode()
                this.denominatorUnit = denominator
            }
        }
        return this
    }

    async convertToSame(measure) {
        const unit = Measure.getLess(await this.getSafeUnit(), await measure.getSafeUnit())
        // console.log('convertToSame', unit)
        const denominator = this.denominatorUnit !== null && measure.denominatorUnit !== null
            ? Measure.getLess(this.denominatorUnit, measure.denominatorUnit)
            : null
        this.convert(unit, denominator)
        //On  ignore l'eslint prefer-object-spread
        // eslint-disable-next-line prefer-object-spread
        const convertedMeasure = Object.assign({}, measure).convert(unit, denominator)
        measure.setCode(this.code)
        measure.setValue(this.value)
        measure.setUnit(this.unit)
        measure.setDenominator(this.denominator)
        measure.setDenominatorUnit(this.denominatorUnit)
        return convertedMeasure
    }

    static async setQuantityToMinDelivery(localData, objectWithMinDelivery, quantityFields = ['requestedQuantity']) {
        // console.log('setQuantityToMinDelivery', objectWithMinDelivery.minDelivery.code)
        const minDeliveryMeasure = new Measure(objectWithMinDelivery.minDelivery.code, objectWithMinDelivery.minDelivery.value)
        const units = await Measure.getOptionsUnit()
        for (const quantityField of quantityFields) {
            if (localData[quantityField] && localData[quantityField].code !== null) {
                // console.log('localMeasure', localData[quantityField].code)
                const localMeasure = new Measure(localData[quantityField].code, localData[quantityField].value)
                if (!localMeasure.isGreaterThanOrEqual(minDeliveryMeasure)) {
                    // la quantité demandée est inférieure à la quantité minimale de livraison
                    localData[quantityField].code = units.find(unit => unit.code === objectWithMinDelivery.minDelivery.code)['@id']
                    localData[quantityField].value = objectWithMinDelivery.minDelivery.value
                }
            } else {
                const code = units.find(unit => unit.code === objectWithMinDelivery.minDelivery.code)['@id']
                let value = objectWithMinDelivery.minDelivery.value
                // L'unité de la quantité demandée n'est pas définie mais peut-être que la bvaleur est définie, dans ce cas on prendra le max entre cette valeur et la valeur de la quantité minimale de livraison
                if (localData[quantityField] && localData[quantityField].value !== null && localData[quantityField].value > value) {
                    value = localData[quantityField].value
                }
                localData[quantityField] = {
                    code,
                    value
                }
            }
        }
    }
    //endregion

    static getLess(measure1, measure2) {
        if (measure1 === null) return measure2
        if (measure2 === null) return measure1
        return measure1.base < measure2.base ? measure1 : measure2
    }
}

export default Measure
