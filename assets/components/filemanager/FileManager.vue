<script setup>
    import VJstree from 'v-VJstree'
    import VueContext from 'vue-context'

</script>

<template>
    <BOverlay :opacity=".9" :show="loading" variant="transparent">
        <VJstree :data="dir" :key="key" @item-click="onClick" ref="tree">
            <template #default="{model, vm}">
                <span @contextmenu.prevent="e => onContextMenuShow(e, model, vm)">
                    <template v-if="!model.loading">
                        <span :class="{'tree-themeicon-custom': !!model.icon}"
                              class="fa-stack tree-icon tree-themeicon"
                              role="presentation"
                              v-if="isExpirableNode(vm)">
                            <i :class="model.icon" class="fa-stack-1x" v-if="!!model.icon"/>
                            <i class="fa fa-hourglass-half fa-stack-1x pl-2 pt-2"/>
                        </span>
                        <i :class="vm.themeIconClasses" role="presentation" v-else/>
                    </template>
                    <span v-html="model.text"></span>
                    <span v-if="!!model.expirationDate">  {{ model.formattedExpirationDate }}</span>
                </span>
            </template>
        </VJstree>
        <VueContext ref="menu">
            <li v-show="isCurrentDir && hasRight">
                <a @click.prevent="onAddFileMenuClick" href="#">
                    <i class="fa fa-file-upload"/>
                    Ajouter un fichier
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && !isCurrentDir">
                <a @click.prevent="onPreview" href="#">
                    <i class="fa fa-eye"/>
                    Aperçu
                </a>
            </li>
            <li v-show="isCurrentDir && hasRight">
                <a @click.prevent="addFolderModal.show" href="#">
                    <i class="fa fa-folder-plus"/>
                    Créer un dossier
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && hasRight">
                <a @click.prevent="renameFolderModal.show" href="#" v-if="isCurrentDir">
                    <i class="fa fa-edit"/>
                    Renommer
                </a>
                <a @click.prevent="updateModal.show" href="#" v-else>
                    <i class="fa fa-edit"/>
                    Modifier
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && hasRight">
                <a @click.prevent="removeModal.show" href="#">
                    <i class="fa fa-trash"/>
                    Supprimer
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && !isCurrentDir">
                <a @click.prevent="download" href="#">
                    <i class="fa fa-file-download"/>
                    Télécharger
                </a>
            </li>
        </VueContext>
        <form class="d-none" enctype="multipart/form-data" ref="form-file" v-if="hasRight">
            <input :value="currentPath" name="dir" type="hidden"/>
            <input @change="onAddFile" name="file" ref="input-file" type="file"/>
        </form>
        <BModal ref="add-expirable-file-modal" title="Ajouter un fichier" v-if="hasRight">
            <template #default="{ok}">
                <BOverlay :opacity=".9" :show="loading" variant="transparent">
                    <form :id="`add-expirable-file-${currentPath}`"
                          @submit.prevent="onAddExpirableFile(ok)"
                          enctype="multipart/form-data"
                          ref="form-expirable-file">
                        <BFormGroup
                            :invalid-feedback="getError(`add-expirable-file-${currentPath}`,'file')"
                            :state="hasError(`add-expirable-file-${currentPath}`,'file')"
                            label="Fichier">
                            <b-form-file
                                :state="hasError(`add-expirable-file-${currentPath}`,'file')"
                                autofocus
                                browse-text="Choisir"
                                drop-placeholder="Glisser le fichier ici"
                                name="file"
                                placeholder="Aucun fichier choisi"
                                required/>
                        </BFormGroup>
                        <BFormGroup
                            :invalid-feedback="getError(`add-expirable-file-${currentPath}`,'expiration-date')"
                            :state="hasError(`add-expirable-file-${currentPath}`,'expiration-date')"
                            label="Date d'expiration">
                            <app-form-datepicker
                                :state="hasError(`add-expirable-file-${currentPath}`,'expiration-date')"
                                name="expiration-date"
                                required/>
                        </BFormGroup>
                        <input :value="currentPath" name="dir" type="hidden"/>
                    </form>
                </BOverlay>
            </template>
            <template #modal-footer="{cancel}">
                <b-button
                    :disabled="loading"
                    :form="`add-expirable-file-${currentPath}`"
                    type="submit"
                    variant="success">
                    Créer
                </b-button>
                <b-button
                    :disabled="loading"
                    :form="`add-expirable-file-${currentPath}`"
                    @click="cancel"
                    type="reset"
                    variant="danger">
                    Annuler
                </b-button>
            </template>
        </BModal>
        <b-modal ref="add-folder-modal" title="Créer un dossier" v-if="hasRight">
            <template #default="{ok}">
                <b-overlay :opacity=".9" :show="loading" variant="transparent">
                    <b-form :id="`add-folder-${currentPath}`" @submit.prevent="onAddFolder(ok)" ref="form-folder">
                        <b-form-group
                            :invalid-feedback="getError(`add-folder-${currentPath}`,'folder')"
                            :state="hasError(`add-folder-${currentPath}`,'folder')"
                            label="Nouveau dossier">
                            <b-form-input
                                :state="hasError(`add-folder-${currentPath}`,'folder')"
                                autofocus
                                name="folder"
                                required/>
                        </b-form-group>
                        <input :value="currentPath" name="dir" type="hidden"/>
                    </b-form>
                </b-overlay>
            </template>
            <template #modal-footer="{cancel}">
                <b-button :disabled="loading" :form="`add-folder-${currentPath}`" type="submit" variant="success">
                    Créer
                </b-button>
                <b-button
                    :disabled="loading"
                    :form="`add-folder-${currentPath}`"
                    @click="cancel"
                    type="reset"
                    variant="danger">
                    Annuler
                </b-button>
            </template>
        </b-modal>
        <b-modal :title="`Renommer ${currentFullName}`" ref="rename-folder-modal" v-if="hasRight">
            <template #default="{ok}">
                <b-overlay :opacity=".9" :show="loading" variant="transparent">
                    <b-form :id="`rename-folder-${currentPath}`"
                            @submit.prevent="onRenameFolder(ok)"
                            ref="form-rename-folder">
                        <b-form-group
                            :invalid-feedback="getError(`rename-folder-${currentPath}`,'new-name')"
                            :state="hasError(`rename-folder-${currentPath}`,'new-name')"
                            label="Nouveau nom">
                            <b-form-input
                                :state="hasError(`rename-folder-${currentPath}`,'new-name')"
                                autofocus
                                name="new-name"
                                required/>
                        </b-form-group>
                        <input :value="currentPath" name="path" type="hidden"/>
                    </b-form>
                </b-overlay>
            </template>
            <template #modal-footer="{cancel}">
                <b-button :disabled="loading" :form="`rename-folder-${currentPath}`" type="submit" variant="success">
                    Renommer
                </b-button>
                <b-button
                    :disabled="loading"
                    :form="`rename-folder-${currentPath}`"
                    @click="cancel"
                    type="reset"
                    variant="danger">
                    Annuler
                </b-button>
            </template>
        </b-modal>
        <b-modal :title="`Modifier ${currentFullName}`" ref="update-modal" v-if="hasRight">
            <template #default="{ok}">
                <b-overlay :opacity=".9" :show="loading" variant="transparent">
                    <b-form :id="`update-${currentPath}`" @submit.prevent="onRename(ok)" ref="form-update">
                        <b-form-group
                            :invalid-feedback="getError(`update-${currentPath}`,'new-name')"
                            :state="hasError(`update-${currentPath}`,'new-name')"
                            label="Nom">
                            <b-input-group :append="currentExt">
                                <b-form-input
                                    :state="hasError(`update-${currentPath}`,'new-name')"
                                    :value="currentName"
                                    autofocus
                                    name="new-name"
                                    required/>
                            </b-input-group>
                        </b-form-group>
                        <b-form-group
                            :invalid-feedback="getError(`update-${currentPath}`,'expiration-date')"
                            :state="hasError(`update-${currentPath}`,'expiration-date')"
                            label="Date d'expiration"
                            v-if="isCurrentExpirable">
                            <app-form-datepicker
                                :state="hasError(`update-${currentPath}`,'expiration-date')"
                                :value="currentExpirationDate"
                                name="expiration-date"
                                required/>
                        </b-form-group>
                        <input :value="currentPath" name="path" type="hidden"/>
                    </b-form>
                </b-overlay>
            </template>
            <template #modal-footer="{cancel}">
                <b-button :disabled="loading" :form="`update-${currentPath}`" type="submit" variant="success">
                    Modifier
                </b-button>
                <b-button
                    :disabled="loading"
                    :form="`update-${currentPath}`"
                    @click="cancel"
                    type="reset"
                    variant="danger">
                    Annuler
                </b-button>
            </template>
        </b-modal>
        <b-modal body-bg-variant="danger" footer-bg-variant="danger" header-bg-variant="danger" ref="remove-modal" v-if="hasRight">
            <template #modal-title>
                <b-badge variant="danger">Avertissement suppression</b-badge>
            </template>
            <b-overlay :opacity=".9" :show="loading" variant="transparent">
                <b-alert :show="currentPath !== null" v-if="currentPath !== null" variant="danger">
                    Voulez-vous vraiment supprimer <strong>{{ currentPath }}</strong>&nbsp;?
                    <div v-if="isCurrentDir">Tous les sous-dossiers et sous-fichiers seront supprimés.</div>
                </b-alert>
            </b-overlay>
            <template #modal-footer="{cancel, ok}">
                <b-button :disabled="loading" @click.prevent="onRemove(ok)" variant="danger">Supprimer</b-button>
                <b-button :disabled="loading" @click.prevent="cancel" variant="secondary">Annuler</b-button>
            </template>
        </b-modal>
        <b-modal
            @hidden="onErrorModalClose"
            body-bg-variant="danger"
            footer-bg-variant="danger"
            header-bg-variant="danger"
            ok-only
            ok-title="Fermer"
            ok-variant="success"
            ref="error-modal">
            <template #modal-title>
                <b-badge variant="danger">
                    Erreur
                </b-badge>
            </template>

            <b-alert :show="errorUnique.length > 0" v-if="errorUnique.length > 0" variant="danger">
                {{ errorUnique }}
            </b-alert>
        </b-modal>
        <b-modal
            :title="`Aperçu de ${currentFullName}`"
            ok-only
            ok-title="Fermer"
            ok-variant="success"
            ref="preview-modal">
            <b-img :src="previewSrc"/>
        </b-modal>
    </BOverlay>
</template>
