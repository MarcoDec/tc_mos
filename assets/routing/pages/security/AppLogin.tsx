import AppCard from '../../../components/bootstrap-5/card/AppCard'
import AppForm from '../../../components/bootstrap-5/form/AppForm.vue'
import {Field} from '../../../store/bootstrap-5/Field'
import type {VNode} from '@vue/runtime-core'

export default (): VNode => <AppCard>
    <AppForm fields={[new Field('username')]} id="login"/>
</AppCard>
