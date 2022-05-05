import {EntityRepository, VatMessage} from '../../modules'

export default class VatMessageRepository extends EntityRepository {
    use = VatMessage
    url = '/api/vat-messages'

    get options() {
        return [...this.all().map(unit => unit.option)].sort((a, b) => a.text.localeCompare(b.text))
    }
}
