import {EntityRepository, OutTrainer} from '../../modules'

export default class OutTrainerRepository extends EntityRepository {
    use = OutTrainer
    url = '/api/out-trainers'

    save(records, vue = null) {
        if (Array.isArray(records)) {
            const saved = []
            for (const record of records)
                saved.push(this.save(record, vue))
            return saved
        }
        if (typeof records.address.id !== 'string') {
            records.address.outTrainerId = records.id
            records.address.id = `${this.use.entity}-${records.id}`
        }
        return super.save(records, vue)
    }
}
