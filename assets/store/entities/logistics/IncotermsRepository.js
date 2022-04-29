import {EntityRepository, Incoterms} from '../../modules'

export default class IncotermsRepository extends EntityRepository {
    use = Incoterms
    url = '/api/incoterms'
}
