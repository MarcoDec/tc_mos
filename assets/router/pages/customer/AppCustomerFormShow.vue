<script setup>
import { computed, reactive, ref, toRefs } from "vue";
import { useCustomerStore } from "../../../stores/customers/customers";
import { useCustomerAttachmentStore } from "../../../stores/customers/customerAttachment";
import generateCustomer from "../../../stores/customers/customer";
import useOptions from "../../../stores/option/options";
import { useSocietyStore } from "../../../stores/societies/societies";
import { useIncotermStore } from "../../../stores/incoterm/incoterm";
import generateSocieties from "../../../stores/societies/societie";

const fecthOptions = useOptions("countries");
await fecthOptions.fetch();

const fetchCustomerStore = useCustomerStore();
const fetchCustomerAttachmentStore = useCustomerAttachmentStore();
const fecthSocietyStore = useSocietyStore();
const fecthIncotermStore = useIncotermStore();

await fetchCustomerStore.fetch();
fetchCustomerAttachmentStore.fetch();
await fetchCustomerStore.fetchInvoiceTime();
await fecthSocietyStore.fetch();
await fecthIncotermStore.fetch();

const societyId = Number(fetchCustomerStore.customer.society.match(/\d+/));
await fecthSocietyStore.fetchById(societyId);
const dataCustomers = computed(() =>
  Object.assign(fetchCustomerStore.customer, fecthSocietyStore.item)
);

const value = computed(() => dataCustomers.value.incoterms["@id"]);
const incotermsValue = reactive(ref(value.value));

const listCustomers = computed(() =>
  reactive({ ...dataCustomers.value, incotermsValue })
);

const optionsIncoterm = computed(() =>
  fecthIncotermStore.incoterms.map((incoterm) => {
    const text = incoterm.name;
    const value = incoterm["@id"];
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
const optionsInvoice = computed(() =>
  fetchCustomerStore.invoicesData.map((invoice) => {
    const text = invoice.name;
    const value = invoice["@id"];
    const optionList = { text, value };
    return optionList;
  })
);
const options = [
  { text: "aaaaa", value: "aaaaa" },
  { text: "bbbb", value: "bbbb" },
];
const Qualitéfields = [
  { label: "Qualité", name: "Qualité", type: "number" },
  { label: "Nb PPM", name: "ppmRate", type: "number" },
  { label: "url *", name: "getUrl", type: "text" },
  { label: "ident", name: "getUsername", type: "text" },
  { label: "password", name: "getPassword", type: "password" },
];
const Logistiquefields = [
  { label: "Nombre de bordereau ", name: "nbDeliveries", type: "number" },
  { label: "DuréeTransport", name: "conveyanceDuration", type: "measure" },
  { label: "Encours maximum", name: "outstandingMax", type: "measure" },
  { label: "Url *", name: "getUrl", type: "text" },
  { label: "Ident", name: "getUsername", type: "text" },
  { label: "Password", name: "getPassword", type: "password" },
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
  { label: "Commande minimum", name: "orderMin", type: "measure" },
];
const Comptabilitéfields = [
  { label: "compte de comptabilité", name: "accountingAccount", type: "text" },
  { label: "forcer la TVA", name: "forceVat", type: "text" },
  {
    label: "montant de la facture minimum",
    name: "invoiceMin",
    type: "measure",
  },
  {
    label: "message TVA",
    name: "messageTVA",
    options: {
      label: (value) =>
        options.find((option) => option.type === value)?.text ?? null,
      options,
    },
    type: "select",
  },
  {
    label: "conditions de paiement",
    name: "paymentTerms",
    options: {
      label: (value) =>
        optionsInvoice.value.find((option) => option.type === value)?.text ??
        null,
      options: optionsInvoice.value,
    },
    type: "select",
  },
  { label: "nb de factures", name: "nbInvoices", type: "number" },
  { label: "url *", name: "getUrl", type: "text" },
  { label: "ident", name: "getUsername", type: "text" },
  { label: "password", name: "getPassword", type: "password" },
  { label: "Envoi facture par email", name: "invoiceByEmail", type: "boolean" },
];
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
const Géneralitésfields = [{ label: "Note", name: "notes", type: "textarea" }];
const Fichiersfields = [{ label: "Fichier", name: "file", type: "file" }];
function updateFichiers(value) {
  console.log("updateFichiers value==", value);
  const customerId = Number(value["@id"].match(/\d+/)[0]);
  const form = document.getElementById("addFichiers");
  const formData = new FormData(form);
  console.log("formData**", formData.get("file"));

  const data = {
    category: "doc",
    customer: `/api/customers/${customerId}`,
    file: formData.get("file"),
  };
  console.log("data Fichiers**", data);

  fetchCustomerAttachmentStore.ajout(data);
}
async function updateQte(value) {
  console.log("value", value);
  const form = document.getElementById("addQualite");
  const formData = new FormData(form);
  const data = {};
  console.log("data===", data);
}
async function updateLogistique(value) {
  console.log("value", value);
  const customerId = Number(value["@id"].match(/\d+/)[0]);
  const form = document.getElementById("addLogistique");
  const formData = new FormData(form);
  console.log("form", formData.get("incotermsValue"));
  console.log("nbDeliveries", formData.get("nbDeliveries"));
  console.log("customerId", customerId);
  const data = {
    accountingPortal: {
      password: "afef",
      url: "https://www.monsite.fr",
      username: "afef",
    },
    nbDeliveries: JSON.parse(formData.get("nbDeliveries")),
    nbInvoices: 3,
    outstandingMax: {
      code: "EUR",
      value: 1,
    },
  };
  const item = generateCustomer(value);

  await item.update(data);

  await fetchCustomerStore.fetch();
  console.log("data===", data);
}
async function updateComp(value) {
  console.log("value", value);
  const form = document.getElementById("addComptabilite");
  const formData = new FormData(form);
  const data = {};
  console.log("data===", data);
}
async function updateGeneral(value) {
  console.log("value", value);
  const customerId = Number(value["@id"].match(/\d+/)[0]);
  const form = document.getElementById("addGeneralites");
  const formData = new FormData(form);
  console.log("form", formData.get("notes"));
  const data = {
    notes: formData.get("notes"),
  };
  const item = generateCustomer(value);

  await item.updateMain(data);

  await fetchCustomerStore.fetch();
  console.log("data===", data);
}
  console.log("fecthSocietyStore===", fecthSocietyStore);
  console.log("fetchCustomerStore===", fetchCustomerStore);
 const result = generateSocieties(fecthSocietyStore.item)
  console.log("item iciiii===", result);

async function updateAdresse(value) {
  console.log("value", value);
  //const customerId = Number(value["@id"].match(/\d+/));
  const societyId = Number(value["@id"].match(/\d+/));
  const form = document.getElementById("addAdresses");
  const formData = new FormData(form);
  console.log('address', formData.get("getCity"));
  console.log("societyId",societyId);
  const data = {
    address: {
      address: formData.get("getAddress"),
      address2: formData.get("getAddress2"),
      city:  formData.get("getCity"),
      country:  formData.get("getCountry"),
      email: formData.get("getEmail"),
      phoneNumber: formData.get("getPhone"),
      zipCode: formData.get("getPostal"),
    },
  };
 
    await fetchCustomerStore.fetch();
    await fecthSocietyStore.update(data, societyId);
    await fecthSocietyStore.fetch();
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
        :component-attribute="listCustomers"
        @update="updateGeneral(listCustomers)"
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
        :component-attribute="listCustomers"
        @update="updateFichiers(listCustomers)"
      />
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
        :component-attribute="dataCustomers"
        @update="updateQte(dataCustomers)"
      />
    </AppTab>
    <AppTab
      id="gui-start-purchase-logistics"
      title="Logistique"
      icon="pallet"
      tabs="gui-start"
    >
      <AppCardShow
        id="addLogistique"
        :fields="Logistiquefields"
        :component-attribute="listCustomers"
        @update="updateLogistique(listCustomers)"
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
        :component-attribute="listCustomers"
        @update="updateComp(listCustomers)"
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
        :component-attribute="listCustomers"
        @update="updateAdresse(listCustomers)"
      />
    </AppTab>
    <AppTab
      id="gui-start-contacts"
      title="Contacts"
      icon="file-contract"
      tabs="gui-start"
    >
      <AppCardShow id="addContacts" />
    </AppTab>
  </AppTabs>
</template>
