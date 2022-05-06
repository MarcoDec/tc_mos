import {EntityRepository, Unit} from '../../modules'

export default class UnitRepository extends EntityRepository {
    use = Unit
    url = '/api/units'

    get options() {
        return [...this.all().map(unit => unit.option)].sort((a, b) => a.text.localeCompare(b.text))
    }

    get optionsId() {
        return [...this.all().map(unit => unit.optionId)].sort((a, b) => a.text.localeCompare(b.text))
    }
}
