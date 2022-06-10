import {Currency, UnitRepository} from '../../modules'

export default class CurrencyRepository extends UnitRepository {
    url = '/api/currencies'
    use = Currency

    filter(active, code, name) {
        const query = this.query()
        if (active)
            query.orWhere('active', active)
        if (code)
            query.orWhere(currency => currency.code.toUpperCase().startsWith(code.toUpperCase()))
        if (name)
            query.orWhere(currency => currency.name.toUpperCase().startsWith(name.toUpperCase()))
        return this.split(query.orderBy('code').get())
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(vue, this.url, 'get', {})
        this.destroyAll(vue)
        this.save(response['hydra:member'], vue)
        this.finish(vue)
    }

    split(toSplit = []) {
        const currencies = toSplit.length > 0 ? toSplit : this.orderBy('code').get()
        const splitted = [[]]
        let i = 0
        for (const currency of currencies) {
            if (splitted[i].length >= 7)
                splitted[++i] = []
            splitted[i].push(currency)
        }
        return splitted
    }
}
