<script>
export default {
    props: ['index', 'resourceName', 'resource', 'resourceId', 'field'],
    computed: {
        values() {
            return Object.entries(this.field.value).map(([locale, value]) => {
                const { label, flag } = this.field.locales[locale]
                return {
                    locale,
                    value,
                    label,
                    flag
                }
            })
        }
    }
}
</script>

<template>
    <PanelItem :index="index" :field="field">
        <template v-slot:value>
            <div class="nt-space-y-4">
                <template v-for="{ locale, label, value, flag } in values">
                    <div class="nt-flex nt-flex-row nt-items-start nt-gap-x-4">
                        <div class="nt-flex-none nt-inline-flex nt-items-center">
                            <Avatar :src="`https://cdn.jsdelivr.net/gh/hampusborgos/country-flags@main/svg/${flag}.svg`"
                                class="nt-w-10 nt-h-auto nt-shadow nt-object-cover nt-rounded-sm" />
                        </div>
                        <div class="nt-grow">
                            <div class="nt-space-y-1 grid">
                                <label class="nt-text-base nt-font-semibold">
                                    {{ __(label) }}:
                                </label>
                                <span v-text="value" />
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </PanelItem>
</template>
