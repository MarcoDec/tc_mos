import {EntityRepository, QualityType} from '../../modules'

export default class QualityTypeRepository extends EntityRepository {
    use = QualityType
    url = '/api/quality-types'
}
