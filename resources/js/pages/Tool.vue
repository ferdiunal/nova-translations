<template>
    <div>

        <Head title="Nova Translation Tool" />

        <Heading class="mb-6">Nova Translation {{ loading ? 'Loading' : '' }}</Heading>

        <Card>
        </Card>
    </div>
</template>

<script>

export default {
    name: 'NovaTranslationsTool',
    computed: {
        // singularName() {
        //     return "action-events";
        // },
        // resourceName() {
        //     return "action-events";
        // },
        // loading() {
        //     return window.NovaTranslations.state("langs").loading;
        // },
        // resourceResponse() {
        //     return window.NovaTranslations.state("langs").langs;
        // },
        // resources() {
        //     return this.resourceData?.resources ?? [];
        // },
        // resourceResponseError() {
        //     return null;
        // },
        // relationshipType() {
        //     return "";
        // },
        // allActions() {
        //     return []
        // },
        // authorizedToRelate() {
        //     return true;
        // },
        // authorizedToCreate() {
        //     return true;
        // },
    },
    async mounted() {
        // await window.NovaTranslations.dispatch("langs", "fetchLangs");
    },
    methods: {

    }
}
</script>

<style>
/* Scoped Styles */
</style>
