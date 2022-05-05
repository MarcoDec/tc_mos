import {EntityRepository, InvoiceTimeDue} from '../../modules'

export default class InvoiceTimeDueRepository extends EntityRepository {
    use = InvoiceTimeDue
    url = '/api/invoice-time-dues'

    get options() {
        return [...this.all().map(unit => unit.option)].sort((a, b) => a.text.localeCompare(b.text))
    }
}
