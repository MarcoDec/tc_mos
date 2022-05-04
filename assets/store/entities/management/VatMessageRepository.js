import {EntityRepository, VatMessage} from '../../modules'

export default class VatMessageRepository extends EntityRepository {
    use = VatMessage
    url = '/api/vat-messages'
}
