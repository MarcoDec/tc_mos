import {EntityRepository, RejectType} from '../../../modules'

export default class RejectTypeRepository extends EntityRepository {
    use = RejectType
    url = '/api/reject-types'
}
