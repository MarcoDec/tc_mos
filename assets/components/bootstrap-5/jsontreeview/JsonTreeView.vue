<script lang="ts" setup>
    import type {ItemData, ValueTypes} from './jsontreeviewytem.d'
    import {computed, defineEmits, defineProps} from 'vue'
    import {ItemType} from './jsontreeviewytem.d'
    import JsonTreeViewItem from './JsonTreeViewItem.vue'

    const emit = defineEmits<(e: 'selected') => void>()
    const props = defineProps(
        {
            colorScheme: {
                default: 'light',
                required: false,
                type: String,
                validator: (value: string) => ['light', 'dark'].indexOf(value) !== -1
            },
            data: {
                default: '',
                required: false,
                type: String

            },
            maxDepth: {
                default: 1,
                required: false,
                type: Number
            },
            rootKey: {
                default: '/',
                required: false,
                type: String
            }
        }
    )
    function itemSelected(data: unknown): void {
        emit('selected', data)
    }

    function build(
        key: string,
        value: ValueTypes,
        depth: number,
        path: string,
        includeKey: boolean
    ): ItemData {
        if (value instanceof Object) {
            if (value instanceof Array) {
                const children = value.map((element, index) =>
                    build(
                        index.toString(),
                        element,
                        depth + 1,
                        includeKey ? `${path}${key}[${index}].` : `${path}`,
                        false
                    ))
                return {
                    children,
                    depth,
                    key,
                    length: children.length,
                    path,
                    type: ItemType.ARRAY
                }
            }

            const children = Object.entries(value).map(([childKey, childValue]) =>
                build(
                    childKey,
                    childValue,
                    depth + 1,
                    includeKey ? `${path}${key}.` : `${path}`,
                    true
                ))
            return {
                children,
                depth,
                key,
                length: children.length,
                path,
                type: ItemType.OBJECT
            }
        }
        return {
            depth,
            key,
            path: includeKey ? `${path}${key}` : path.slice(0, -1),
            type: ItemType.VALUE,
            value
        }

    }

    const parsed = computed(
        (): ItemData => {
            const json = props.data

            if (typeof json !== 'undefined') {
                const data = JSON.parse(json)
                if (data instanceof Object) {
                    return build(props.rootKey, {...data}, 0, '', true)
                }
            }
            return {
                depth: 0,
                key: props.rootKey,
                path: '',
                type: ItemType.VALUE,
                value: props.data
            }
        }
    )
</script>

<template>
    <JsonTreeViewItem
        :class="[{'root-item': true, dark: colorScheme === 'dark'}]"
        :data="parsed"
        :max-depth="maxDepth"
        @selected="itemSelected"/>
    <slot/>
</template>

<style lang="scss" scoped>
.root-item {
  --jtv-key-color: #0977e6;
  --jtv-valueKey-color: #073642;
  --jtv-string-color: #268bd2;
  --jtv-number-color: #2aa198;
  --jtv-boolean-color: #cb4b16;
  --jtv-null-color: #6c71c4;
  --jtv-arrow-size: 6px;
  --jtv-arrow-color: #444;
  --jtv-hover-color: rgba(0, 0, 0, 0.1);
  margin-left: 0;
  width: 100%;
  height: auto;
}
.root-item.dark {
  --jtv-key-color: #80d8ff;
  --jtv-valueKey-color: #fdf6e3;
  --jtv-hover-color: rgba(255, 255, 255, 0.1);
  --jtv-arrow-color: #fdf6e3;
}
</style>
