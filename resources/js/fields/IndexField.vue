<script>
export default {
    props: ['resourceName', 'resource', 'resourceId', 'field'],
    computed: {
        values() {
            return Object.entries(this.field.value).map(([locale, value]) => {
                return {
                    locale,
                    value,
                    flag: this.field.locales[locale].flag
                }
            })
        }
    }
}
</script>

<template>
    <div>
        <div className="nt-flex -nt-space-x-2 nt-overflow-hidden">
            <template v-for="{ locale, value, flag } in values">
                <img :alt="locale" :src="`https://cdn.jsdelivr.net/gh/hampusborgos/country-flags@main/svg/${flag}.svg`"
                    className="nt-inline-block nt-rounded-full nt-border nt-h-6 nt-w-6 nt-ring-2 hover:nt-z-[99] hover:nt-bg-white hover:dark:nt-bg-black/35 nt-transition-all nt-ring-white"
                    v-tooltip="`${locale.toUpperCase()}: ${value}`" />
            </template>
        </div>
    </div>
</template>
