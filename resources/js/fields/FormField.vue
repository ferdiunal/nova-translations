<script>
import { DependentFormField, Errors, HandlesValidationErrors } from 'laravel-nova';
import TranslationField from "../components/TranslationField";
import Translaters from '../components/Translaters.vue';
export default {
    mixins: [HandlesValidationErrors, DependentFormField],
    inject: ['removeFile'],
    expose: ['beforeRemove'],
    props: ['resourceName', 'resourceId', 'field'],
    components: {
        Translaters,
        TranslationField,
    },
    computed: {
        locales() {
            return this.field.locales;
        },
        defaultAttributes() {
            return {
                rows: this.currentField.rows,
                class: this.errorClasses,
                placeholder: this.field.name,
            }
        },
        extraAttributes() {
            const attrs = this.currentField.extraAttributes

            return {
                ...this.defaultAttributes,
                ...attrs,
            }
        },
        disableNonDefaultFields() {
            return [
                "", undefined, null
            ].includes(
                this.field.value[window.Nova.config('locale')]
            )
        },
        currentFieldValue() {
            return this.field.value[window.Nova.config('locale')]
        },
    },
    data() {
        return {
            value: this.field.value,
            loading: false,
        }
    },
    methods: {
        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            try {
                const attr = this.fieldAttribute
                for (const v in this.value) {
                    formData.append(`${attr}[${v}]`, this.value?.[v] ?? "")
                }
            } catch (e) {
                console.log(e)
            }
        },
        async translate(key) {
            try {
                this.loading = true;
                const data = await window.Nova.request().post(`${window.NovaTranslations.url}/all-translate/${this.resourceName}/${this.resourceId}`, {
                    source: window.Nova.config('locale'),
                    targets: Object.keys(this.locales),
                    translater: key,
                    attribute: this.field.attribute
                })

                this.value = data.data?.text
            } catch (e) {
            } finally {
                this.loading = false
            }
        },
    }
}
</script>

<template>
    <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText" :full-width-content="fullWidthContent">
        <template #field>
            <LoadingView variant="overlay" :loading="loading">
                <div class="nt-grid">
                    <template v-if="resourceId">
                        <div class="grid">
                            <span class="nt-text-lg nt-font-bold">
                                {{ __('Translate to All Langs') }}:
                            </span>
                            <Translaters :field="field" @click="translate" />
                        </div>
                        <DividerLine class="nt-my-6" />
                    </template>
                    <template v-for="(v, k) of locales">
                        <TranslationField :resourceId="resourceId" :resourceName="resourceName" v-model="value[k]"
                            :currentField="currentField" :field="field" :placeholder="placeholder"
                            :extraAttributes="extraAttributes" :localeKey="k" :locale="v"
                            :disableNonDefaultFields="disableNonDefaultFields" :currentValue="currentFieldValue" />
                    </template>
                </div>
            </LoadingView>
        </template>
    </DefaultField>
</template>
