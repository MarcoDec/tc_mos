import api from '../../../../../../api'

class Unit {
    children = []
    constructor(code, name, parent, base) {
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
        return this.code
    }

    setCode(code) {
        this.code = code
        return this
    }

    getBase() {
        return this.base
    }

    setBase(base) {
        this.base = base
        return this
    }

    getName() {
        return this.name
    }

    setName(name) {
        this.name = name
        return this
    }

    getParent() {
        return this.parent
    }

    setParent(parent) {
        this.parent = parent
        return this
    }

    getChildren() {
        return this.children
    }

    addChild(children) {
        if (!this.children.contains(children)) {
            this.children.add(children)
            children.setParent(this)
        }
        return this
    }

    removeChild(children) {
        if (this.children.contains(children)) {
            this.children.removeElement(children)
            if (children.getParent() === this) {
                children.setParent(null)
            }
        }
        return this
    }

    getDistanceBase() {
        return this.base > 1 ? this.base : 1 / this.base
    }

    getDistance(unit) {
        return this.getDistanceBase() * unit.getDistanceBase()
    }

    async getRoot() {
        const root = new Unit()
        let rootParent = this.parent
        let rootData = ''
        while (rootParent !== null) {
            // eslint-disable-next-line no-await-in-loop
            rootData = await api(rootParent, 'GET')
            root.initFromApi(rootData)
            rootParent = root.getParent()
        }
        return root
    }

    getDepthChildren() {
        const children = this.getChildren().map(child => child.getDepthChildren().push(child))
        // on récupère une collection d'enfant ayant des codes uniques
        return children.reduce((acc, child) => acc.concat(child), [])
    }

    async getFamily() {
        const root = await this.getRoot()
        root.getDepthChildren().push(root)
        // on récupère une collection d'enfant ayant des codes uniques
        return root.getDepthChildren().reduce((acc, child) => acc.concat(child), [])
    }

    async has(unit) {
        if (typeof this.code === 'undefined' || this.code === null) throw Error('No code defined.')
        const unitFamily = await this.getFamily()
        return unit !== null && (unit.getCode() === this.getCode() || unitFamily.contains(member => member.getId() === unit.getId()))
    }

    getLess(unit) {
        return this.base < unit.base ? this : unit
    }

    assertSameAs(unit) {
        if (typeof this.code === 'undefined' || this.code === null) throw Error('No code defined.')
        if (unit === null || unit.code === null) {
            throw new Error('No code defined.')
        }
        if (!this.has(unit)) {
            throw new Error(`Units ${this.code} and ${unit.code} aren't on the same family.`)
        }
    }

    getConvertorDistance(unit) {
        const distance = this.getDistance(unit)
        return this.isLessThan(unit) ? 1 / distance : distance
    }
}

export default Unit
