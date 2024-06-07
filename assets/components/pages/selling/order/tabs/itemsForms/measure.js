import api from '../../../../../../api'

class Unit {
    children = []
    constructor(code, name, parent, base) {
        console.log('constructor Unit', code, name, parent, base)
        this.code = code
        this.name = name
        this.parent = parent
        this.base = base
    }
    initFromApi(data) {
        this.code = data.code
        this.name = data.name
        this.base = data.base
        this.parent = data.parent
    }
    getCode() {
        return this.code;
    }
    setCode(code) {
        this.code = code;
        return this;
    }
    getBase() {
        return this.base;
    }
    setBase(base) {
        this.base = base;
        return this;
    }
    getName() {
        return this.name;
    }
    setName(name) {
        this.name = name;
        return this;
    }
    getParent() {
        return this.parent;
    }
    setParent(parent) {
        this.parent = parent;
        return this;
    }
    getChildren() {
        return this.children;
    }
    addChild(children) {
        if (!this.children.contains(children)) {
            this.children.add(children);
            children.setParent(this);
        }
        return this;
    }
    removeChild(children) {
        if (this.children.contains(children)) {
            this.children.removeElement(children);
            if (children.getParent() === this) {
                children.setParent(null);
            }
        }
        return this;
    }
    getDistanceBase() {
        return this.base > 1 ? this.base : 1 / this.base;
    }
    getDistance(unit) {
        return this.getDistanceBase() * unit.getDistanceBase();
    }
    async getRoot() {
        let rootParent = this.parent
        let rootData = ''
        while (rootParent!== null) {
            rootData = await api(root.parent, 'GET')
            rootParent = rootData.parent
        }
        let root = new Unit()
        root.initFromApi(rootData)
        return root;
    }
    getDepthChildren() {
        const children = this.getChildren().map(child => child.getDepthChildren().push(child));
        return children.unique(child => child.getCode());
    }
    async getFamily() {
        const root = await this.getRoot();
        return root.getDepthChildren().push(root).unique(child => child.getId());
    }
    has(unit) {
        const unitFamily = this.getFamily();
        return unit !== null && (unit.getCode() === this.getCode() || unitFamily.contains(member => member.getId() === unit.getId()));
    }
    getLess(unit) {
        return this.base < unit.base ? this : unit;
    }
    assertSameAs(unit) {
        if (unit === null || unit.code === null) {
            throw new Error('No code defined.');
        }
        if (!this.has(unit)) {
            throw new Error(`Units ${this.code} and ${unit.code} aren't on the same family.`);
        }
    }
    getConvertorDistance(unit) {
        const distance = this.getDistance(unit);
        return this.isLessThan(unit) ? 1 / distance : distance;
    }
}


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
                    console.log('pas de tarif trouvé')
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
        console.log('constructor Measure', code, value, denominator, denominatorUnit)
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
        return this.code;
    }
    setCode(code) {
        this.code = code;
        return this;
    }
    async getSafeUnit() {
        if (this.unit === null) {
            //on récupère l'unité ayant le code this.code
            const unitData = await Measure.getUnitByCode(this.code);
            this.unit = new Unit(unitData.code, unitData.name, unitData.parent, unitData.base)
        }
        return this.unit;
    }
    getValue() {
        return this.value;
    }
    setValue(value) {
        this.value = value;
        return this;
    }
    getUnit() {
        return this.unit;
    }
    setUnit(unit) {
        this.unit = unit;
        return this;
    }
    getDenominator() {
        return this.denominator;
    }
    setDenominator(denominator) {
        this.denominator = denominator;
        return this;
    }
    getDenominatorUnit() {
        return this.denominatorUnit;
    }
    setDenominatorUnit(denominatorUnit) {
        this.denominatorUnit = denominatorUnit;
        return this;
    }
    //endregion

    //region méthodes de conversion et calculs
    isGreaterThanOrEqual(measure) {
        console.log('isGreaterThanOrEqual', this.code)
        const clone = new Measure(this.code, this.value, this.denominator, this.denominatorUnit)
        measure = clone.convertToSame(measure);
        return clone.value >= measure.value;
    }
    async add(measure) {
        if (measure.code === null || measure.code === '') throw new Error('add(measure) une unité de mesure doit être définie')
        switch (this.type) {
        case 'unit':
            if (measure.type !== 'unit') throw new Error('add(measure) les deux mesures doivent être de type unité')
            measure.unit = measure.unit ?? await Measure.getUnitByCode(measure.code)
            break
        case 'currency':
            if (measure.type !== 'currency') throw new Error('add(measure) les deux mesures doivent être de type devise')
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
        measure = this.convertToSame(measure)
        this.value = this.value + measure.value
        return this
    }
    substract(measure) {
        return this.add(measure.setValue(-measure.value));
    }
    convert(unit, denominator = null) {
        const safeUnit = this.getSafeUnit();
        safeUnit.assertSameAs(unit);
        if (safeUnit.getCode() !== unit.getCode()) {
            this.value *= safeUnit.getConvertorDistance(unit);
            this.code = unit.getCode();
            this.unit = unit;
        }

        if (denominator !== null) {
            if (this.denominator === null) {
                throw new Error('No denominator.');
            }
            if (this.denominatorUnit === null) {
                throw new Error('Unit not loaded.');
            }
            this.denominatorUnit.assertSameAs(denominator);
            if (this.denominatorUnit.getCode() !== denominator.getCode()) {
                this.value *= 1 / this.denominatorUnit.getConvertorDistance(denominator);
                this.denominator = denominator.getCode();
                this.denominatorUnit = denominator;
            }
        }
        return this;
    }
    async convertToSame(measure) {
        const unit = Measure.getLess(await this.getSafeUnit(), await measure.getSafeUnit());
        console.log('convertToSame', unit)
        const denominator =
            this.denominatorUnit !== null && measure.denominatorUnit !== null
                ? Measure.getLess(this.denominatorUnit, measure.denominatorUnit)
                : null;
        this.convert(unit, denominator);
        return Object.assign({}, measure).convert(unit, denominator);
    }

    async setQuantityToMinDelivery(localData, objectWithMinDelivery, quantityFields = ['requestedQuantity']) {
        // console.log('setQuantityToMinDelivery', objectWithMinDelivery.minDelivery.code)
        const minDeliveryMeasure = new Measure(objectWithMinDelivery.minDelivery.code, objectWithMinDelivery.minDelivery.value)
        for (const quantityField of quantityFields) {
            if (localData[quantityField] && localData[quantityField].code !== null) {
                console.log('localMeasure', localData[quantityField].code)
                const localMeasure = new Measure(localData[quantityField].code, localData[quantityField].value)
                if (!localMeasure.isGreaterThanOrEqual(minDeliveryMeasure)) {
                    // la quantité demandée est inférieure à la quantité minimale de livraison
                    localData[quantityField].code = (await Measure.getOptionsUnit()).find(unit => unit.code === objectWithMinDelivery.minDelivery.code)['@id']
                    localData[quantityField].value = objectWithMinDelivery.minDelivery.value
                }
            } else {
                const code = (await Measure.getOptionsUnit()).find(unit => unit.code === objectWithMinDelivery.minDelivery.code)['@id']
                let value = objectWithMinDelivery.minDelivery.value
                // L'unité de la quantité demandée n'est pas définie mais peut-être que la bvaleur est définie, dans ce cas on prendra le max entre cette valeur et la valeur de la quantité minimale de livraison
                if (localData[quantityField] && localData[quantityField].value !== null && localData[quantityField].value > value) {
                    value = localData[quantityField].value
                }
                localData[quantityField] = {
                    code:  code,
                    value: value
                }
            }
        }
    }
    //endregion
    static getLess(measure1, measure2) {
        return measure1.base < measure2.base ? measure1 : measure2;
    }
}

export default Measure
