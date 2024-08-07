<script>
import { Localization } from 'laravel-nova'
export default {
    props: ['field', 'disabled'],
    emits: ['click'],
    mixins: [Localization],
}
</script>
<template>
    <div class="flex flex-row items-center nt-py-1.5">
        <template v-for="({ key, title, icon }, i) in field.translaters">
            <BasicButton :disabled="disabled" type="button" @click="$emit('click', key)"
                class="inline-flex flex-row items-center nt-gap-x-1.5 disabled:nt-cursor-not-allowed" v-tooltip="__('Translate with :translater', {
                    translater: title
                })">
                <img :src="icon" :alt="title" class="nt-h-6 nt-w-auto nt-object-contain" />
            </BasicButton>
            <div v-if="i < (Object.keys(field.translaters).length - 1)"
                class="nt-h-6 nt-border mx-4 nt-border-[#D8E3EC]"></div>
        </template>
    </div>
</template>
