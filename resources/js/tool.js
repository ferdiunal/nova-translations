import IndexField from "./fields/IndexField.vue";
import DetailField from "./fields/DetailField.vue";
import FormField from "./fields/FormField.vue";

import Tool from "./pages/Tool";
import LangsStore from "./store/langs";
import App from "./app";

Nova.booting((app, store) => {
    window.NovaTranslations = App;
    store.registerModule(App.namespaces.langs, LangsStore);

    Nova.inertia("NovaTranslations", Tool);
    app.component("index-nova-translations", IndexField);
    app.component("detail-nova-translations", DetailField);
    app.component("form-nova-translations", FormField);
});
