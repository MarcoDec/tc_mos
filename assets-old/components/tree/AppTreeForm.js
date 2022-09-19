import {computed, h, onMounted, ref, resolveComponent, watch} from 'vue'
import AppTreeAttributes from './AppTreeAttributes'
import {generateFields} from '../props'

export default {
    props: {
        attributes: {required: true, type: Object},
        families: {required: true, type: Object},
        fields: generateFields(),
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        noDisplayAttr: {type: Boolean}
    },
    setup(props) {
        const displayAttr = computed(() => !props.noDisplayAttr && props.families.hasSelected)
        const fields = computed(() => [
            {label: 'Parent', name: 'parent', options: props.families, type: 'select'},
            ...props.fields
        ])
        const selected = computed(() => props.families.selected)
        const selectedForm = computed(() => selected.value?.form(fields.value) ?? {file: '/img/no-image.png'})
        const title = computed(() => selected.value?.fullName ?? 'Ajouter une famille')
        const value = ref(null)
        const attrFormId = computed(() => `${props.id}-attr`)
        const formId = computed(() => `${props.id}-create`)
        const titles = computed(() => (displayAttr.value ? [title.value, 'Attributs'] : title.value))

        function blur() {
            props.machine.send('submit')
            props.families.blur()
            props.machine.send('success')
        }

        async function remove() {
            if (selected.value)
                await selected.value.remove()
        }

        async function submit(data) {
            props.machine.send('submit')
            try {
                if (selected.value)
                    await selected.value.update(props.fields, data)
                else
                    await props.families.create(props.fields, data)
                props.machine.send('success')
            } catch (violations) {
                props.machine.send('fail', {violations})
            }
        }

        function updateValue() {
            value.value = {...selectedForm.value}
        }

        updateValue()

        onMounted(updateValue)
        watch(selectedForm, updateValue)

        return () => h(
            resolveComponent('AppCard'),
            {id: props.id, title: titles.value},
            () => {
                const renderedForm = h(
                    resolveComponent('AppForm'),
                    {
                        class: 'col',
                        disabled: props.machine.state.value.matches('loading'),
                        fields: fields.value,
                        id: formId.value,
                        labelCols: 'col-xl-3 col-lg-12',
                        modelValue: value.value,
                        noIgnoreNull: props.families.hasSelected,
                        onSubmit: submit,
                        'onUpdate:modelValue'(v) {
                            value.value = v
                        },
                        submitLabel: 'Créer',
                        violations: props.machine.state.value.context.violations
                    },
                    ({disabled, form, submitLabel, type}) => (props.families.hasSelected
                        ? [
                            h(
                                resolveComponent('AppBtn'),
                                {class: 'me-2', disabled, form, type},
                                () => 'Modifier'
                            ),
                            h(
                                resolveComponent('AppBtn'),
                                {
                                    class: 'me-2',
                                    disabled,
                                    onClick: blur,
                                    variant: 'warning'
                                },
                                () => 'Annuler'
                            ),
                            h(
                                resolveComponent('AppBtn'),
                                {
                                    disabled,
                                    onClick: remove,
                                    variant: 'danger'
                                },
                                () => 'Supprimer'
                            )
                        ]
                        : h(resolveComponent('AppBtn'), {disabled, form, type}, () => submitLabel))
                )

                function renderImg(col = 0) {
                    return h(
                        'div',
                        {class: col > 0 ? `col-${col}` : 'col'},
                        h('img', {class: 'col img-thumbnail', src: value.value.file})
                    )
                }

                return displayAttr.value
                    ? h(
                        'div',
                        {class: 'row tree-card-body'},
                        h(
                            'div',
                            {class: 'col'},
                            h('div', {class: 'row'}, renderedForm),
                            h(
                                'div',
                                {class: 'row'},
                                renderImg()
                            )
                        ),
                        h(AppTreeAttributes, {
                            attributes: props.attributes,
                            family: selected.value,
                            form: attrFormId.value,
                            machine: props.machine
                        })
                    )
                    : h('div', {class: 'row'}, [
                        renderedForm,
                        renderImg(4)
                    ])
            }
        )
    }
}
