<script setup>
import AppRowsTablePage from "./AppRowsTablePage.vue";
import AppSuspense from '../../../components/AppSuspense.vue'
import useOptions from '../../../stores/option/options'


const fetchUnitOptions = useOptions('units')
await fetchUnitOptions.fetchOp()
const optionsUnit = fetchUnitOptions.options.map(op => {
    const text = op.text
    const value = op.value
    return {text, value}
})

const fieldsComponenentSuppliers = [
  {
      create: true,
      filter: true,
      label: 'Proportion',
      name: 'proportion',
      prefix: 'componentSuppliers',
      sort: false,
      type: 'text',
      update: true
  },
  {
      create: true,
      label: 'Délai',
      name: 'delai',
      prefix: 'componentSuppliers',
      measure: {
          code: {
              label: 'Code',
              name: 'delai.code',
              options: {
                  label: value =>
                  optionsUnit.find(option => option.type === value)?.text ?? null,
                  options: optionsUnit
              },
              type: 'select'
          },
          value: {
              label: 'Valeur',
              name: 'delai.value',
              type: 'number',
              step: 0.1
          }
      },
      type: 'measure',
      update: true
  },
  {
      create: false,
      label: 'Moq',
      name: 'moq',
      prefix: 'componentSuppliers',
      measure: {
          code: {
              label: 'Code',
              name: 'moq.code',
              options: {
                  label: value =>
                  optionsUnit.find(option => option.type === value)?.text ?? null,
                  options: optionsUnit
              },
              type: 'select'
          },
          value: {
              label: 'Valeur',
              name: 'moq.value',
              type: 'number',
              step: 0.1
          }
      },
      type: 'measure',
      update: true
  },
  {
      filter: true,
      label: 'Poids cu',
      name: 'poidsCu',
      prefix: 'componentSuppliers',
      measure: {
          code: {
              label: 'Code',
              name: 'poidsCu.code',
              options: {
                  label: value =>
                  optionsUnit.find(option => option.type === value)?.text ?? null,
                  options: optionsUnit
              },
              type: 'select'
          },
          value: {
              label: 'Valeur',
              name: 'poidsCu.value',
              type: 'number',
              step: 0.1
          }
      },
      type: 'measure',
      update: true
  },
  {
      create: false,
      filter: true,
      label: 'Référence',
      name: 'reference',
      prefix: 'componentSuppliers',
      sort: false,
      type: 'text',
      update: true
  },
  {
      create: true,
      filter: true,
      label: 'Indice',
      name: 'indice',
      prefix: 'componentSuppliers',
      sort: false,
      type: 'number',
      update: true
  },
  {
      create: true,
      label: 'incoterms',
      name: 'incoterms',
      prefix: 'componentSuppliers',
      type: 'text',
      update: true
  },
  {
      filter: true,
      label: 'packaging',
      name: 'packaging',
      prefix: 'componentSuppliers',
      measure: {
          code: {
              label: 'Code',
              name: 'packaging.code',
              options: {
                  label: value =>
                  optionsUnit.find(option => option.type === value)?.text ?? null,
                  options: optionsUnit
              },
              type: 'select'
          },
          value: {
              label: 'Valeur',
              name: 'packaging.value',
              type: 'number',
              step: 0.1
          }
      },
      type: 'measure',
      update: true
  },
  {
      create: true,
      label: 'packagingKind',
      name: 'packagingKind',
      prefix: 'componentSuppliers',
      type: 'number',
      update: true
  },
  {
      children: [
          {create: true, filter: true, label: '€', name: 'price', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
          {create: true, filter: true, label: 'Q', name: 'quantite', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
          {create: true, filter: true, label: 'ref', name: 'ref', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true}
      ],
      create: false,
      filter: true,
      label: 'Prix',
      name: 'prices',
      prefix: 'componentSuppliers',
      sort: false,
      type: 'text',
      update: true
  }
]

const fieldsComponenentSuppliersPrices = [
  {
    create: true,
    filter: true,
    label: '€',
    name: 'price',
    prefix: 'componentSupplierPrices',
    measure: {
          code: {
              label: 'Code',
              name: 'price.code',
              options: {
                  label: value =>
                  optionsUnit.find(option => option.type === value)?.text ?? null,
                  options: optionsUnit
              },
              type: 'select'
          },
          value: {
              label: 'Valeur',
              name: 'price.value',
              type: 'number',
              step: 0.1
          }
      },
    type: 'measure',
    update: true
  },
  {
    create: true,
    filter: true,
    label: 'Q',
    name: 'quantity',
    prefix: 'componentSupplierPrices',
    measure: {
        code: {
            label: 'Code',
            name: 'quantity.code',
            options: {
                label: value =>
                optionsUnit.find(option => option.type === value)?.text ?? null,
                options: optionsUnit
            },
            type: 'select'
        },
        value: {
            label: 'Valeur',
            name: 'quantity.value',
            type: 'number',
            step: 0.1
        }
    },
    type: 'measure',
    update: true
  },
  {
    create: true,
    filter: true, 
    label: 'ref', 
    name: 'ref', 
    prefix: 'componentSupplierPrices', 
    sort: false, 
    type: 'number', 
    update: true
  }
]

</script>

<template>
  <AppSuspense>
   <AppRowsTablePage :fieldsComponenentSuppliers="fieldsComponenentSuppliers" :fieldsComponenentSuppliersPrices="fieldsComponenentSuppliersPrices"/>
  </AppSuspense>
</template>
