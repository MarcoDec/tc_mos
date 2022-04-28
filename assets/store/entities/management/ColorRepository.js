import {Color, EntityRepository} from '../../modules'

export default class ColorRepository extends EntityRepository {
    use = Color
    url = '/api/colors'
}
