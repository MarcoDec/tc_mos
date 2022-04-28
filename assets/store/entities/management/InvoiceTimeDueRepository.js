import {EntityRepository, InvoiceTimeDue} from '../../modules'

export default class InvoiceTimeDueRepository extends EntityRepository {
    use = InvoiceTimeDue
    url = '/api/invoice-time-dues'
}
