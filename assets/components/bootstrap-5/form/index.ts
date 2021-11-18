import type {Field, FieldGetter} from '../../../store/bootstrap-5/Field'
import type {Axios} from 'axios'
import type {Component} from '@vue/runtime-core'
import type {ExtractGetterTypes} from 'vuex-composition-helpers/dist/types/util'
import type {Ref} from 'vue'
import {defineAsyncComponent} from 'vue'

type AppFormData = {
    axios: Axios
    storedFields: Ref<readonly string[]>
}
type AppFormMethods = {submit: (e: Event) => void}
type AppFormProps = {
    action: string
    fields: Field[]
    id: string
    method?: 'delete' | 'get' | 'patch' | 'post'
}
type AppFormComponent = Component<AppFormProps, never, AppFormData, never, AppFormMethods>
export const AppForm = defineAsyncComponent<AppFormComponent>(async () => import('./AppForm.vue'))

type AppFormGroupData = {
    form: Ref<string>
    id: ExtractGetterTypes<FieldGetter>
    label: Ref<string>
    name: Ref<string>
    type: Ref<'password' | 'text'>
}
type AppFormGroupProps = {field: string}
type AppFormGroupComponent = Component<AppFormGroupProps, never, AppFormGroupData>
export const AppFormGroup = defineAsyncComponent<AppFormGroupComponent>(async () => import('./AppFormGroup.vue'))

export const AppInput = defineAsyncComponent(async () => import('./AppInput.vue'))

type AppLabelProps = {cols?: number}
type AppLabelComponent = Component<AppLabelProps>
export const AppLabel = defineAsyncComponent<AppLabelComponent>(async () => import('./AppLabel.vue'))
