import {EntityRepository, Incoterms} from '../../modules'

export default class IncotermsRepository extends EntityRepository {
    use = Incoterms
    url = '/api/incoterms'

    get options() {
        return [...this.all().map(unit => unit.option)].sort((a, b) => a.text.localeCompare(b.text))
    }
}
