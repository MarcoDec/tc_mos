<script setup>
    import {ref} from 'vue'
    defineProps({
        node: {
            required: true,
            type: Object
        }
    })
    const isOpen = ref(false)
    const toggle = () => {
        isOpen.value = !isOpen.value
    }
    const showChildUrl = childNode => {
        if (childNode.url) {
            window.open(childNode.url, '_blank')
        }
    }
</script>

<template>
    <div class="tree-node">
        <span class="icon"><Fa :icon="node.icon"/></span>
        <!-- <span class="label" @click="toggle"  @click.once="showChildUrl(node)">{{ node.label }} ({{ node.children ? node.children.length : 0 }})</span> -->
        <span class="label" @click="toggle" @click.once="showChildUrl(node)">{{ node.label }}</span>
        <div v-if="isOpen" class="divChil">
            <div v-for="child in node.children" :key="child.id">
                <MyTree :node="child"/>
            </div>
        </div>
    </div>
</template>

<style>
/* Styles for the Tree component */
.divChil {
  margin-left: 10px;
}
.tree {
  font-family: Arial, sans-serif;
}

/* Styles for the Tree Node component */
.tree-node {
  align-items: center;
  margin: 4px 0;
  flex-direction: column;
  padding-left: 30px;
    background-color: lightyellow;
}

.icon {
  margin-right: 4px;
  color: #ffc107;
}

.label {
  font-weight: 600;
  margin-right: 8px;
  cursor: pointer;
}

ul {
  margin: 4px 0;
  padding-left: 16px;
}

li {
  margin: 4px 0;
}
</style>
