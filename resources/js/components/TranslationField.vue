<script>
import { Localization } from 'laravel-nova'
import Translaters from './Translaters.vue';
export default {
    mixins: [Localization],
    props: ['modelValue', 'disableNonDefaultFields', 'resourceName', 'resourceId', 'currentField', 'field', 'placeholder', 'extraAttributes', 'localeKey', 'locale', 'currentValue'],
    emits: ['update:modelValue'],
    components: {
        Translaters
    },
    computed: {
        value: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        isTranslater() {
            return window.Nova.config('locale') !== this.localeKey
        }
    },
    data() {
        return {
            loading: false
        }
    },
    methods: {
        async translate(key) {
            try {
                this.loading = true;
                const data = await window.Nova.request().post(`${window.NovaTranslations.url}/translate/${this.resourceName}/${this.resourceId ?? 'creation'}`, {
                    source: window.Nova.config('locale'),
                    target: this.localeKey,
                    translater: key,
                    attribute: this.field.attribute,
                    currentValue: this.currentValue ?? null
                })

                this.value = data.data?.text
            } catch (e) {
                console.log(e)
            } finally {
                this.loading = false
            }
        },
    }
}
</script>
<template>
    <div class="relative">
        <div v-if="loading" dusk="loading-view"
            class="absolute inset-0 z-20 bg-white/75 dark:bg-gray-800/75 flex items-center justify-center p-6">
            <Loader class="text-gray-600" width="30" v-if="loading" />
        </div>

        <div class="nt-flex nt-flex-row nt-items-start nt-gap-x-4">
            <div class="nt-flex-none nt-inline-flex nt-items-center">
                <label :for="`${currentField.uniqueKey}-${k}`">
                    <Avatar :src="`https://cdn.jsdelivr.net/gh/hampusborgos/country-flags@main/svg/${locale.flag}.svg`"
                        class="nt-w-10 nt-h-auto nt-shadow nt-object-cover nt-rounded-sm" />
                </label>
            </div>
            <div class="nt-grow">
                <div class="nt-space-y-1">
                    <label :for="`${currentField.uniqueKey}-${k}`" class="nt-text-base nt-font-semibold">
                        {{ __(locale.label) }}
                    </label>
                    <div class="nt-flex nt-flex-col">
                        <textarea v-bind="extraAttributes"
                            :disabled="disableNonDefaultFields && isTranslater || field.disabled"
                            class="nt-block nt-w-full form-control form-input form-control-bordered nt-py-3 nt-h-auto"
                            :id="`${currentField.uniqueKey}-${k}`" :dusk="field.attribute" v-model="value"
                            :maxlength="field.enforceMaxlength ? field.maxlength : -1" :placeholder="placeholder" />

                        <CharacterCounter v-if="field.maxlength" :count="value[k].length" :limit="field.maxlength" />
                        <div class="grid mt-4" v-if="isTranslater">
                            <span class="nt-text-sm nt-font-bold">
                                {{ __('Translaters') }}:
                            </span>
                            <Translaters :field="field" :disabled="disableNonDefaultFields && isTranslater"
                                @click="translate" />
                        </div>
                        <HelpText v-else class="nt-mt-1.5 nt-text-sm nt-font-normal">
                            {{ __('Default Locale cannot be translated.') }}
                        </HelpText>
                    </div>
                </div>
            </div>
        </div>
        <DividerLine class="nt-my-4" />
    </div>
</template>
