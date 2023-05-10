<script setup>
import { computed, ref } from "vue";
import generateSupplier from "../../../stores/supplier/supplier";
import generateSocieties from "../../../stores/societies/societie";
import generateSupplierContact from "../../../stores/supplier/supplierContact";
import { useIncotermStore } from "../../../stores/incoterm/incoterm";
import useOptions from "../../../stores/option/options";
import { useSocietyStore } from "../../../stores/societies/societies";
import { useSupplierAttachmentStore } from "../../../stores/supplier/supplierAttachement";
import { useSupplierContactsStore } from "../../../stores/supplier/supplierContacts";
import { useSuppliersStore } from "../../../stores/supplier/suppliers";
import MyTree from "../../../components/MyTree.vue";
import { useTableMachine } from "../../../composable/xstate";
import { log } from "xstate/lib/actions";

const emit = defineEmits(["update", "update:modelValue", "rating"]);
const isError = ref(false);
const isShow = ref(false);
const violations = ref([]);
const fecthCurrencyOptions = useOptions("currencies");
const fecthCompanyOptions = useOptions("companies");
const fecthOptions = useOptions("countries");
const fetchSuppliersStore = useSuppliersStore();
const fecthIncotermStore = useIncotermStore();
const fetchSocietyStore = useSocietyStore();
const fecthSupplierAttachmentStore = useSupplierAttachmentStore();
const fecthSupplierContactsStore = useSupplierContactsStore();
await fetchSuppliersStore.fetch();
await fetchSuppliersStore.fetchVatMessage();
await fecthIncotermStore.fetch();
await fetchSocietyStore.fetch();
await fecthCurrencyOptions.fetchOp();
await fecthSupplierAttachmentStore.fetch();
await fecthOptions.fetchOp();
await fecthCompanyOptions.fetchOp();
console.log("fetchSuppliersStore", fetchSuppliersStore.suppliers);
const supplierAttachment = computed(() =>
  fecthSupplierAttachmentStore.supplierAttachment.map((attachment) => ({
    id: attachment["@id"],
    label: attachment.url.split("/").pop(), // get the filename from the URL
    icon: "file-contract",
    url: attachment.url,
  }))
);
const treeData = computed(() => {
  const data = {
    id: 1,
    label: "Attachments" + `(${supplierAttachment.value.length})`,
    icon: "folder",
    children: supplierAttachment.value,
  };
  return data;
});
const optionsVatMessageForce = [
  {
    text: "TVA par défaut selon le pays du client",
    value: "TVA par défaut selon le pays du client",
  },
  { text: "Force AVEC TVA", value: "Force AVEC TVA" },
  { text: "Force SANS TVA", value: "Force SANS TVA" },
];
const optionsCompany = computed(() =>
  fecthCompanyOptions.options.map((op) => {
    const text = op.text;
    const value = op["@id"];
    const optionList = { text, value };
    return optionList;
  })
);
const optionsCountries = computed(() =>
  fecthOptions.options.map((op) => {
    const text = op.text;
    const value = op.id;
    const optionList = { text, value };
    return optionList;
  })
);
const optionsCurrency = computed(() =>
  fecthCurrencyOptions.options.map((op) => {
    const text = op.text;
    const value = op.value;
    const optionList = { text, value };
    return optionList;
  })
);
const optionsVat = computed(() =>
  fetchSuppliersStore.vatMessage.map((op) => {
    const text = op.name;
    const value = op["@id"];
    const optionList = { text, value };
    return optionList;
  })
);

const societyId = Number(fetchSuppliersStore.suppliers.society.match(/\d+/));
const supplierId = Number(fetchSuppliersStore.suppliers.id);
await fetchSocietyStore.fetchById(societyId);
await fecthSupplierContactsStore.fetchBySociety(societyId);
console.log("fecthSupplierContactsStore", fecthSupplierContactsStore);
console.log("societyId==", societyId);
const itemsTable = computed(() =>
  fecthSupplierContactsStore.itemsSocieties.reduce(
    (acc, curr) => acc.concat(curr),
    []
  )
);
console.log("itemsTable==", itemsTable);

const dataSuppliers = computed(() =>
  Object.assign(fetchSuppliersStore.suppliers, fetchSocietyStore.item)
);
const managed = computed(() => {
  const data = { managedCopper: fetchSocietyStore.item.copper.managed };
  return data;
});
const list = computed(() =>
  Object.assign(fetchSocietyStore.item, managed.value)
);

const listSuppliers = computed(() =>
  Object.assign({}, fetchSuppliersStore.suppliers, list.value)
);
console.log("helooo", listSuppliers);
const optionsIncoterm = computed(() =>
  fecthIncotermStore.incoterms.map((incoterm) => {
    const text = incoterm.name;
    const value = incoterm["@id"];
    const optionList = { text, value };
    return optionList;
  })
);

const fieldsSupp = [
  {
    label: "Nom ",
    name: "name",
    type: "text",
  },
  {
    label: "Prenom ",
    name: "surname",

    type: "text",
  },
  {
    label: "Mobile ",
    name: "mobile",

    type: "text",
  },
  {
    label: "Email ",
    name: "email",

    type: "text",
  },
  {
    label: "Adresse",
    name: "address",
    type: "text",
  },
  {
    label: "Complément d'adresse ",
    name: "address2",
    type: "text",
  },
  {
    label: "Pays",
    name: "country",
    options: {
      label: (value) =>
        optionsCountries.value.find((option) => option.type === value)?.text ??
        null,
      options: optionsCountries.value,
    },
    type: "select",
  },
  {
    label: "Ville ",
    name: "city",
    type: "text",
  },
  {
    label: "Code Postal ",
    name: "zipCode",
    type: "text",
  },
];

const Géneralitésfields = [
  { label: "Nom", name: "name", type: "text" },
  {
    label: "Compagnies",
    name: "administeredBy",
    options: {
      label: (value) =>
        optionsCompany.value.find((option) => option.type === value)?.text ??
        null,
      options: optionsCompany.value,
    },
    type: "select",
  },
  { label: "Langue", name: "language", type: "text" },
  { label: "Note", name: "notes", type: "textarea" },
];
const Qualitéfields = [
  { label: "Gestion de la qualité", name: "managedQuality", type: "boolean" },
  { label: "Niveau de confiance", name: "confidenceCriteria", type: "rating" },
  { label: "Taux de PPM", name: "ppmRate", type: "number" },
];
const Achatfields = [
  { label: "Minimum de commande", name: "orderMin", type: "measure" },
  {
    label: "Incoterm",
    name: "incotermsValue",
    options: {
      label: (value) =>
        optionsIncoterm.value.find((option) => option.type === value)?.text ??
        null,
      options: optionsIncoterm.value,
    },
    type: "select",
  },
  { label: "Gestion du cuivre", name: "managedCopper", type: "boolean" },
  { label: "Niveau de confiance", name: "confidenceCriteria", type: "rating" },
  { label: "Commande ouverte", name: "openOrdersEnabled", type: "boolean" },
  { label: "Accusé de réception", name: "ar", type: "boolean" },
];
const Comptabilitéfields = [
  {
    label: "Montant minimum de facture",
    name: "invoiceMin",
    type: "measure",
  },
  {
    label: "Devise",
    name: "currency",
    options: {
      label: (value) =>
        optionsCurrency.value.find((option) => option.type === value)?.text ??
        null,
      options: optionsCurrency.value,
    },
    type: "select",
  },
  { label: "Compte de comptabilité", name: "accountingAccount", type: "text" },
  { label: "TVA", name: "vat", type: "text" },
  {
    label: "Forcer la TVA",
    name: "forceVat",
    options: {
      label: (value) =>
        optionsVatMessageForce.find((option) => option.type === value)?.text ??
        null,
      options: optionsVatMessageForce,
    },
    type: "select",
  },
  {
    label: "Message TVA",
    name: "vatMessageValue",
    options: {
      label: (value) =>
        optionsVat.value.find((option) => option.type === value)?.text ?? null,
      options: optionsVat.value,
    },
    type: "select",
  },
];
const Fichiersfields = [{ label: "Fichier", name: "file", type: "file" }];
const Adressefields = [
  { label: "Email", name: "getEmail", type: "text" },
  { label: "Adresse", name: "getAddress", type: "text" },
  { label: "Complément d'adresse", name: "getAddress2", type: "text" },
  { label: "Code postal", name: "getPostal", type: "text" },
  { label: "Ville", name: "getCity", type: "text" },
  {
    label: "Pays",
    name: "getCountry",
    options: {
      label: (value) =>
        optionsCountries.value.find((option) => option.type === value)?.text ??
        null,
      options: optionsCountries.value,
    },
    type: "select",
  },
  { label: "Fax", name: "getPhone", type: "text" },
];
const val = ref(Number(fetchSuppliersStore.suppliers.confidenceCriteria));
async function input(value) {
  val.value = value.confidenceCriteria;
  emit("update:modelValue", val.value);
  const data = {
    confidenceCriteria: val.value,
  };
  const item = generateSupplier(value);
  await item.updateQuality(data);
  await fetchSocietyStore.fetch();
}
async function update(value) {
  const form = document.getElementById("addQualite");
  const formData = new FormData(form);

  const data = {
    managedQuality: JSON.parse(formData.get("managedQuality")),
  };
  const dataSociety = {
    ppmRate: JSON.parse(formData.get("ppmRate"))
   
  };

  const item = generateSupplier(value);
  const itemSoc = generateSocieties(value);
  await itemSoc.update(dataSociety);
  await item.updateQuality(data);
  await fetchSocietyStore.fetch();
}
async function updateGeneral(value) {
  const form = document.getElementById("addGeneralites");
  const formData = new FormData(form);
    console.log("form", formData.get("notes"));

  const data = {
    //managedProduction: JSON.parse(formData.get("managedProduction")),
    //administeredBy:  formData.get("administeredBy"),
    //administeredBy: ["/api/companies/1"],
    language: formData.get("language"),
    notes: formData.get("notes")
  };
  const dataAdmin = {
    administeredBy: [formData.get("administeredBy")],
    name: formData.get("name"),
  };
  console.log("ddd", data);
  const item = generateSupplier(value);
  await item.updateMain(data);
  await item.updateAdmin(dataAdmin);
  await fetchSuppliersStore.fetch();
}
async function updateLogistique(value) {
  const form = document.getElementById("addAchatLogistique");
  const formData = new FormData(form);

  const dataSociety = {
    ar: JSON.parse(formData.get("ar")),
    copper: {
      managed: JSON.parse(formData.get("managedCopper")),
    },
    orderMin: {
      code: formData.get("orderMin-code"),
      value: JSON.parse(formData.get("orderMin-value")),
    },
    incoterms: formData.get("incotermsValue"),
  };
  const data = {
    openOrdersEnabled: JSON.parse(formData.get("openOrdersEnabled")),
  };
  const itemSoc = generateSocieties(value);
  await itemSoc.update(dataSociety);
  const item = generateSupplier(value);
  await item.updateLog(data);  
  await fetchSocietyStore.fetch();
}

async function updateAddress(value) {
  const form = document.getElementById("addAdresses");
  const formData = new FormData(form);

  const data = {
    address: {
      address: formData.get("getAddress"),
      address2: formData.get("getAddress2"),
      city: formData.get("getCity"),
      country: formData.get("getCountry"),
      email: formData.get("getEmail"),
      phoneNumber: formData.get("getPhone"),
      zipCode: formData.get("getPostal"),
    },
  };

  const item = generateSupplier(value);
  await item.updateMain(data);
}
async function updateComptabilite(value) {
  const suppliersId = Number(value["@id"].match(/\d+/)[0]);
  const form = document.getElementById("addComptabilite");
  const formData = new FormData(form);

  const dataSociety = {
    invoiceMin: {
      code: formData.get("invoiceMin-code"),
      value: JSON.parse(formData.get("invoiceMin-value")),
    },
    vat: formData.get("vat"),
    forceVat: formData.get("forceVat"),
    vatMessage: formData.get("vatMessageValue"),
    accountingAccount: formData.get("accountingAccount"),
  };
  const data = {
    currency: formData.get("currency"),
  };
  const item = generateSupplier(value);
  await item.updateAccounting(data);
  const itemSoc = generateSocieties(value);
  await itemSoc.update(dataSociety);
  // await fetchSocietyStore.update(dataSociety, societyId);
  await fetchSocietyStore.fetch();
}
function updateFichiers(value) {
  const suppliersId = Number(value["@id"].match(/\d+/)[0]);
  const form = document.getElementById("addFichiers");
  const formData = new FormData(form);

  const data = {
    category: "doc",
    file: formData.get("file"),
    supplier: `/api/suppliers/${suppliersId}`,
  };

  fecthSupplierAttachmentStore.ajout(data);
  const supplierAttachment = computed(() =>
    fecthSupplierAttachmentStore.supplierAttachment.map((attachment) => ({
      id: attachment["@id"],
      label: attachment.url.split("/").pop(), // get the filename from the URL
      icon: "file-contract",
      url: attachment.url,
    }))
  );
  treeData.value = {
    id: 1,
    label: "Attachments" + `(${supplierAttachment.value.length})`,
    icon: "folder",
    children: supplierAttachment.value,
  };
}
async function ajout(inputValues) {
  const data = {
    address: {
      address: inputValues.address ?? "",
      address2: inputValues.address2 ?? "",
      city: inputValues.city ?? "",
      email: inputValues.email ?? "",
      country: inputValues.country ?? "",
      // phoneNumber: inputValues.getPhone ?? "",
      zipCode: inputValues.zipCode ?? "",
    },
    // default: true,
    // kind: "comptabilité",
    mobile: inputValues.mobile ?? "",
    name: inputValues.name ?? "",
    society: "/api/suppliers/"+supplierId,
    surname: inputValues.surname ?? "",
  };
  try {
    await fecthSupplierContactsStore.ajout(data, societyId);
    isError.value = false;
  } catch (error) {
    console.log("erreuuur==", error);
    if (error === "Internal Server Error") {
      const err = { message: "Internal Server Error" };
      violations.value.push(err);
    } else {
      violations.value = error;
      isError.value = true;
    }
  }
}
async function deleted(id) {
  await fecthSupplierContactsStore.deleted(id);
}
async function updateSuppliers(inputValues) {
  console.log("inpiuutttt", inputValues);
  const id = Number(inputValues["@id"].match(/\d+/));
  console.log("id", id);
  const dataUpdate = {
    address: {
      address: inputValues.address ?? "",
      address2: inputValues.address2 ?? "",
      city: inputValues.city ?? "",
      email: inputValues.email ?? "",
      country: inputValues.country ?? "",
      // phoneNumber: inputValues.getPhone ?? "",
      zipCode: inputValues.zipCode ?? "",
    },
    // default: true,
    // kind: "comptabilité",
    mobile: inputValues.mobile ?? "",
    name: inputValues.name ?? "",
    society: "/api/suppliers/" + supplierId,
    surname: inputValues.surname ?? "",
  };
  try {
    const item = generateSupplierContact(inputValues);
    await item.update(dataUpdate);
    isError.value = false;
  } catch (error) {
    await fecthSupplierContactsStore.fetchBySociety(societyId);
    itemsTable.value = fecthSupplierContactsStore.itemsSocieties.reduce(
      (acc, curr) => acc.concat(curr),
      []
    );
    if (error === "Internal Server Error") {
      const err = { message: "Internal Server Error" };
      violations.value.push(err);
    } else {
      violations.value = error;
      isError.value = true;
    }
  }
}
</script>

<template>
  <AppTabs id="gui-start" class="gui-start-content">
    <AppTab
      id="gui-start-main"
      active
      title="Généralités"
      icon="pencil"
      tabs="gui-start"
    >
      <AppCardShow
        id="addGeneralites"
        :fields="Géneralitésfields"
        :component-attribute="fetchSuppliersStore.suppliers"
        @update="updateGeneral(fetchSuppliersStore.suppliers)"
      />
    </AppTab>
    <AppTab
      id="gui-start-files"
      title="Fichiers"
      icon="laptop"
      tabs="gui-start"
    >
      <AppCardShow
        id="addFichiers"
        :fields="Fichiersfields"
        @update="updateFichiers(fetchSuppliersStore.suppliers)"
      />
      <MyTree :node="treeData" @node-click="openAttachment" />
      <div v-if="selectedAttachment">
        Selected Attachment: {{ selectedAttachment }}
      </div>
    </AppTab>
    <AppTab
      id="gui-start-quality"
      title="Qualité"
      icon="certificate"
      tabs="gui-start"
    >
      <AppCardShow
        id="addQualite"
        :fields="Qualitéfields"
        :component-attribute="listSuppliers"
        @update="update(listSuppliers)"
        @update:modelValue="input"
      />
    </AppTab>
    <AppTab
      id="gui-start-purchase-logistics"
      title="Achat/Logistique"
      icon="bag-shopping"
      tabs="gui-start"
    >
      <AppCardShow
        id="addAchatLogistique"
        :fields="Achatfields"
        :component-attribute="listSuppliers"
        @update="updateLogistique(listSuppliers)"
        @update:modelValue="input"
      />
    </AppTab>
    <AppTab
      id="gui-start-accounting"
      title="Comptabilité"
      icon="industry"
      tabs="gui-start"
    >
      <AppCardShow
        id="addComptabilite"
        :fields="Comptabilitéfields"
        :component-attribute="listSuppliers"
        @update="updateComptabilite(listSuppliers)"
      />
    </AppTab>
    <AppTab
      id="gui-start-addresses"
      title="Adresses"
      icon="location-dot"
      tabs="gui-start"
    >
      <AppCardShow
        id="addAdresses"
        :fields="Adressefields"
        :component-attribute="fetchSuppliersStore.suppliers"
        @update="updateAddress(fetchSuppliersStore.suppliers)"
      />
    </AppTab>
    <AppTab
      id="gui-start-contacts"
      title="Contacts"
      icon="file-contract"
      tabs="gui-start"
    >
      <AppCollectionTable
        v-if="!isShow"
        id="addContacts"
        :fields="fieldsSupp"
        :items="itemsTable"
        @ajout="ajout"
        @deleted="deleted"
        @update="updateSuppliers"
      />

      <div v-if="isError" class="alert alert-danger" role="alert">
        <div v-for="violation in violations" :key="violation">
          <li>{{ violation.message }}</li>
        </div>
      </div>
    </AppTab>
  </AppTabs>
</template>
