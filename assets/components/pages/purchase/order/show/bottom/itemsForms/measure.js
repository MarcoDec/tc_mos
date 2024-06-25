import api from "../../../../../../../api"
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

    static async getAndSetProductPrice(product, supplier, order, quantity, localData, formKey) {
        try {
            const price = await Measure.getProductGridPrice(product, supplier, order, quantity)
            if (typeof price === 'string') window.alert(price)
            else if (price === null) {
                throw new Error('Le prix du produit n\'a pas été trouvé')
            } else {
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.price = {
                    value: price.value,
                    code: currency['hydra:member'][0]['@id']
                }
                formKey.value++
            }
        } catch (error) {
            console.error(error)
        }
    }

    static async getAndSetComponentPrice(component, supplier, order, quantity, localData, formKey) {
        try {
            const price = await Measure.getComponentGridPrice(component, supplier, order, quantity)
            console.log('price', price)
            if (price === null) {
                throw new Error('Le prix du composant n\'a pas été trouvé')
            }
            if (typeof price === 'string') window.alert(price)
            else {
                const currency = await api(`/api/currencies?code=${price.code}`)
                localData.price.value = price.value
                localData.price.code = currency['hydra:member'][0]['@id']
                formKey.value++
            }
        } catch (error) {
            console.error(error)
        }
    }

    static async getComponentGridPrice(component, supplier, order, quantity) {
        const componentIri = component['@id']
        // on récupère le type de produit associé à la commande
        const kind = order.kind
        const supplierIri = supplier['@id']
        // on récupère le componentSupplier associé au composant et au fournisseur
        const componentSupplier = await api(`/api/supplier-components?component=${componentIri}&supplier=${supplierIri}&kind=${kind}`, 'GET')
        // Si il y a au moins une réponse on récupère le premier élément
        if (componentSupplier['hydra:member'].length > 0) {
            // Normalement s'il existe plus d'une grilles tarifaire il faut alerter l'utilisateur
            if (componentSupplier['hydra:member'].length > 1) {
                return `Il existe plus d'une grille tarifaire pour ce composant et ce fournisseur de type ${kind}`
            }
            const componentSupplierItem = componentSupplier['hydra:member'][0]
            // On récupère la grille tarifaire dans SupplierComponentPrice associée au componentSupplier
            const componentSupplierPrices = await api(`/api/supplier-component-prices?component=${componentSupplierItem['@id']}`, 'GET')
            // Si il y a au moins une réponse on parcourt les éléments pour récupérer les prix dont la quantité associée est supérieure ou égale à la quantité demandée
            if (componentSupplierPrices['hydra:member'].length > 0) {
                // On tri les éléments par ordre décroissant de quantité
                componentSupplierPrices['hydra:member'].sort((a, b) => b.quantity.value - a.quantity.value)
                // on récupère le premier élément dont la quantité est supérieure ou égale à la quantité demandée
                const quantityMoreThan1 = quantity.value >= 1 ? quantity.value : 1
                const componentSupplierPricesItems = componentSupplierPrices['hydra:member'].find(price => price.quantity.value <= quantityMoreThan1)
                if (typeof componentSupplierPricesItems === 'undefined') return null
                return componentSupplierPricesItems.price
            }
            return 'Il n\'y a pas de grille de prix pour ce composant et ce fournisseur'
        }
        return 'Ce composant n\'est pas associé à ce fournisseur'
    }

    static async getProductGridPrice(product, supplier, order, quantity) {
        const productIri = product['@id']
        // on récupère le type de produit associé à la commande
        const kind = order.kind
        const supplierIri = supplier['@id']
        // on récupère le productSupplier associé au produit et au fournisseur
        const productSupplier = await api(`/api/supplier-products?product=${productIri}&supplier=${supplierIri}&kind=${kind}`, 'GET')
        // Si il y a au moins une réponse on récupère le premier élément
        if (productSupplier['hydra:member'].length > 0) {
            // Normalement s'il existe plus d'une grilles tarifaire il faut alerter l'utilisateur
            if (productSupplier['hydra:member'].length > 1) {
                return `Il existe plus d'une grille tarifaire pour ce produit ${productIri} et ce fournisseur ${supplierIri} de type ${kind}`
            }
            const productSupplierItem = productSupplier['hydra:member'][0]
            // On récupère la grille tarifaire dans SupplierProductPrice associée au productSupplier
            const productSupplierPrices = await api(`/api/supplier-product-prices?product=${productSupplierItem['@id']}`, 'GET')
            // Si il y a au moins une réponse on parcourt les éléments pour récupérer les prix dont la quantité associée est supérieure ou égale à la quantité demandée
            if (productSupplierPrices['hydra:member'].length > 0) {
                // On tri les éléments par ordre décroissant de quantité
                productSupplierPrices['hydra:member'].sort((a, b) => b.quantity.value - a.quantity.value)
                // on récupère le premier élément dont la quantité est supérieure ou égale à la quantité demandée
                const quantityMoreThan1 = quantity.value >= 1 ? quantity.value : 1
                const productSupplierPricesItems = productSupplierPrices['hydra:member'].find(price => price.quantity.value <= quantityMoreThan1)
                if (typeof productSupplierPricesItems === 'undefined') return null
                return productSupplierPricesItems.price
            }
            return 'Il n\'y a pas de grille de prix pour ce produit et ce fournisseur'
        }
        return 'Ce produit n\'est pas associé à ce fournisseur, il n\'y a pas de grille de prix'
    }
    //endregion

    constructor(code, value, denominator = null, type = 'unit') {
        this.code = code
        this.value = value
        this.denominator = denominator
        this.type = type
        if (code === null || code === '') {
            throw new Error('une unité de mesure doit être définie')
        }
        if (type === null) throw new Error('le type d\'unité de mesure doit être défini')
        if (value === null) {
            this.value = 0.0
        }
    }

    async checkWellLoaded() {
        if (!this.isLoaded) {
            await this.init()
        }
    }

    async init() {
        if (this.type === 'unit') {
            await this.initUnits()
        } else if (this.type === 'currency') {
            await this.initCurrencies()
        }
    }

    async initUnits() {
        // On commence par récupérer les iri des unités de mesure
        this.unitData = (await Measure.getUnitByCode(this.code))['hydra:member'][0]
        this.unit = new Unit(this.unitData.code, this.unitData.name, this.unitData.parent, this.unitData.base)
        if (await this.denominator !== null) {
            this.denominatorUnitData = (await Measure.getUnitByCode(this.denominator))['hydra:member'][0]
            this.denominatorUnit = new Unit(this.denominatorUnitData.code, this.denominatorUnitData.name, this.denominatorUnitData.parent, this.denominatorUnitData.base)
        }
        this.isLoaded = true
    }

    async initCurrencies() {
        this.unitData = (await Measure.getCurrencyByCode(this.code))['hydra:member'][0]
        this.unit = new Unit(this.unitData.code, this.unitData.name, this.unitData.parent, this.unitData.base)
        this.denominator = null
        this.denominatorUnit = null
        this.isLoaded = true
    }

    //region getters et setters
    getCode() {
        return this.code
    }

    async setCode(code) {
        this.code = code
        this.isLoaded = false
        await this.checkWellLoaded()
        return this
    }

    async getSafeUnit() {
        await this.checkWellLoaded()
        if (typeof this.code === 'string') {
            // Si this.code contient /api/units/ on récupère le code de l'unité
            const containsIriApiUnits = this.code.includes('/api/units/')
            if (containsIriApiUnits) throw new Error('getSafeUnit() le code de l\'unité de mesure ne doit pas contenir /api/units/')
        }
        if (typeof this.unit === 'undefined' || this.unit === null) {
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

    async getUnit() {
        await this.checkWellLoaded()
        return this.unit
    }

    async setUnit(unit) {
        this.unit = unit
        this.isLoaded = false
        await this.checkWellLoaded()
        return this
    }

    async getDenominator() {
        await this.checkWellLoaded()
        return this.denominator
    }

    async setDenominator(denominator) {
        this.denominator = denominator
        this.isLoaded = false
        await this.checkWellLoaded()
        return this
    }

    async getDenominatorUnit() {
        await this.checkWellLoaded()
        return this.denominatorUnit
    }

    async setDenominatorUnit(denominatorUnit) {
        this.denominatorUnit = denominatorUnit
        this.isLoaded = false
        await this.checkWellLoaded()
        return this
    }
    //endregion

    //region méthodes de conversion et calculs
    async isGreaterThanOrEqual(measure) {
        const clone = new Measure(this.getCode(), this.getValue(), this.getDenominator(), await this.getDenominatorUnit())
        // measure = clone.convertToSame(measure)
        await clone.convertToSame(measure)
        return clone.value >= measure.value
    }

    async add(measure) {
        if (measure.getCode() === null || measure.getCode() === '') throw new Error('add(measure) une unité de mesure doit être définie')
        switch (this.type) {
        case 'unit':
            if (measure.type !== 'unit') throw new Error('add(measure) les deux mesures doivent être de type unité')
            // eslint-disable-next-line require-atomic-updates
            measure.setUnit(measure.unit ?? await Measure.getUnitByCode(measure.getCode()))
            break
        case 'currency':
            if (measure.type !== 'currency') throw new Error('add(measure) les deux mesures doivent être de type devise')
            // eslint-disable-next-line require-atomic-updates
            measure.setUnit(measure.unit ?? await Measure.getCurrencyByCode(measure.getCode()))
            break
        default:
            throw new Error('add(measure) le type de mesure n\'est pas défini')
        }
        if (!measure.unit) throw new Error(`add(measure) l'unité de mesure ${measure.getCode()} n'a pas été trouvée pour le type ${this.type}`)
        if (await this.getUnit() === null && this.getCode() === null) {
            await this.setUnit(await measure.getUnit())
            await this.setCode(await measure.getCode())
            this.setValue(measure.getValue())
            return this
        }
        if (await this.getUnit() === null) {
            await this.setUnit(await Measure.getUnitByCode(this.getCode()))
        }
        // measure = this.convertToSame(measure)
        await this.convertToSame(measure)
        this.value += measure.value
        return this
    }

    substract(measure) {
        return this.add(measure.setValue(-measure.value))
    }

    async convert(unit, denominator = null) {
        const safeUnit = await this.getSafeUnit()
        safeUnit.assertSameAs(unit)
        if (safeUnit.getCode() !== unit.getCode()) {
            this.value *= safeUnit.getConvertorDistance(unit)
            await this.setCode(unit.getCode())
            await this.setUnit(unit)
        }

        if (denominator !== null) {
            const safeDenominator = await this.getDenominatorUnit()
            if (safeDenominator === null) {
                throw new Error('Convertion error, denominators are not consistent. (one with, the other without)')
            }
            const safeDenominatorUnit = await this.getDenominatorUnit()
            if (safeDenominatorUnit === null) {
                throw new Error('Unit not loaded.')
            }
            safeDenominatorUnit.assertSameAs(denominator)
            if (safeDenominatorUnit.getCode() !== denominator.getCode()) {
                this.value *= 1 / safeDenominatorUnit.getConvertorDistance(denominator)
                await this.setDenominator(denominator.getCode())
                await this.setDenominatorUnit(denominator)
            }
        }
        return this
    }

    clone() {
        return new Measure(this.getCode(), this.getValue(), this.getDenominator(), this.type)
    }

    async convertToSame(measure) {
        const safeUnitThis = await this.getSafeUnit()
        const safeUnitMeasure = await measure.getSafeUnit()
        const unit = Measure.getLess(safeUnitThis, safeUnitMeasure)
        const safeDenominatorUnitThis = await this.getDenominatorUnit()
        const safeDenominatorUnitMeasure = await measure.getDenominatorUnit()
        const denominator = safeDenominatorUnitThis !== null && safeDenominatorUnitMeasure !== null
            ? Measure.getLess(safeDenominatorUnitThis, safeDenominatorUnitMeasure)
            : null
        await this.convert(unit, denominator)
        //On  ignore l'eslint prefer-object-spread
        // On clone la mesure 'me
        // eslint-disable-next-line prefer-object-spread
        const convertedMeasure = measure.clone().convert(unit, denominator)
        measure.setCode(this.getCode())
        measure.setValue(this.getValue())
        await measure.setUnit(await this.getUnit())
        await measure.setDenominator(await this.getDenominator())
        await measure.setDenominatorUnit(await this.getDenominatorUnit())
        return convertedMeasure
    }

    static async setQuantityToMinDelivery(localData, minDeliveryMeasure, quantityFields = ['requestedQuantity']) {
        const units = await Measure.getOptionsUnit()
        let isGreaterThanOrEqual = false
        for (const quantityField of quantityFields) {
            if (localData[quantityField] && localData[quantityField].code !== null) {
                // On désactive eslint pour les await dans une boucle
                // eslint-disable-next-line no-await-in-loop
                const localUnit = await api(localData[quantityField].code, 'GET')
                const localMeasure = new Measure(localUnit.code, localData[quantityField].value)
                // eslint-disable-next-line no-await-in-loop
                isGreaterThanOrEqual = await localMeasure.isGreaterThanOrEqual(minDeliveryMeasure)
                if (!isGreaterThanOrEqual) {
                    // la quantité demandée est inférieure à la quantité minimale de livraison
                    // eslint-disable-next-line require-atomic-updates
                    localData[quantityField] = {
                        code: units.find(unit => unit.code === minDeliveryMeasure.code)['@id'],
                        value: minDeliveryMeasure.value
                    }
                }
            } else {
                const code = units.find(unit => unit.code === minDeliveryMeasure.code)['@id']
                let value = minDeliveryMeasure.value
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
        const test1 = typeof measure1 === 'undefined' || measure1 === null
        const test2 = typeof measure2 === 'undefined' || measure2 === null
        if (test1 && test2) return null
        if (test1) return measure2
        if (test2) return measure1
        return measure1.base < measure2.base ? measure1 : measure2
    }
}

export default Measure
