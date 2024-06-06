import api from '../../../../../../api'

class Unit {
    children = []
    constructor() {
        this.code = null
        this.name = null
        this.parent = null
        this.base = 1
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
        return api('/api/units', 'GET')
    }
    static async getOptionsCurrency() {
        return api('/api/currencies', 'GET')
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
                if (price === null) return
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
            // console.log('productCustomer', productCustomer['hydra:member'])
            // Normalement s'il existe plus d'une grilles tarifaire il faut alerter l'utilisateur
            console.log('productCustomer', productCustomer['hydra:member'])
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
                const productCustomerPricesItems = productCustomerPrices['hydra:member'].find(price => price.quantity.value <= quantity.value)
                console.log('productCustomerPricesItems 1er élément', productCustomerPricesItems)
                // console.log('productCustomerPricesItems 1er élément avec quantité', productCustomerPricesItems)
                if (typeof productCustomerPricesItems === 'undefined') return null
                return productCustomerPricesItems.price
            }
            return 'Il n\'y a pas de grille de prix pour ce produit et ce client'
        }
        return 'Ce produit n\'est pas associé à ce client'
    }
    //endregion

    constructor(code, value, denominator = null, denominatorUnit = null) {
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
        this.unit = await Measure.getUnitByCode(this.code)
        if (this.denominator !== null) {
            this.denominatorUnit = await Measure.getUnitByCode(this.denominator)
        }
    }
    async initCurrencies() {
        this.type = 'currency'
        this.unit = await Measure.getCurrencyByCode(this.code)
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
            this.unit = await Measure.getUnitByCode(this.code);
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
        const clone = Object.assign({}, this);
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
    convertToSame(measure) {
        const unit = Measure.getLess(this.getSafeUnit(), measure.getSafeUnit());
        const denominator =
            this.denominatorUnit !== null && measure.denominatorUnit !== null
                ? Measure.getLess(this.denominatorUnit,measure.denominatorUnit)
                : null;
        this.convert(unit, denominator);
        return Object.assign({}, measure).convert(unit, denominator);
    }
    //TODO: Corriger liste d'options
    setQuantityToMinDelivery(localData, objectWithMinDelivery) {
        console.log('setQuantityToMinDelivery', localData, objectWithMinDelivery)
        // Lors de la sélection d'un produit nous en récupérons les informations de livraison minimale et nous les affectons aux quantités demandées et confirmées
        //console.info('Positionnement MinDelivery à ', objectWith.minDelivery)
        if (localData.requestedQuantity) {
            // En 1ère approximation, nous positionnons la quantité minimale uniquement lorsque sa valeur est supérieure à la valeur actuelle
            if (localData.requestedQuantity.value < objectWithMinDelivery.minDelivery.value) {
                localData.requestedQuantity.code = this.optionsUnit.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value
                localData.requestedQuantity.value = objectWithMinDelivery.minDelivery.value
            }
        } else {
            localData.requestedQuantity = {
                code: this.optionsUnit.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value,
                value: objectWithMinDelivery.minDelivery.value
            }
        }
        if (localData.confirmedQuantity) {
            // En 1ère approximation, nous positionnons la quantité minimale uniquement lorsque sa valeur est supérieure à la valeur actuelle
            if (localData.confirmedQuantity.value < objectWithMinDelivery.minDelivery.value) {
                localData.confirmedQuantity.code = this.optionsUnit.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value
                localData.confirmedQuantity.value = objectWithMinDelivery.minDelivery.value
            }
        } else {
            // console.log(this.optionsUnit)
            localData.confirmedQuantity = {
                code: this.optionsUnit.find(unit => unit.text === objectWithMinDelivery.minDelivery.code).value,
                value: objectWithMinDelivery.minDelivery.value
            }
        }
    }
    //endregion
    static getLess(measure1, measure2) {
        return measure1.base < measure2.base ? measure1 : measure2;
    }
}

export default Measure
