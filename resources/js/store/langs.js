export default {
    namespaced: true,
    state() {
        return {
            loading: false,
            langs: [],
            langsCanceller: undefined,
        };
    },
    mutations: {
        setLangs(state, langs) {
            state.langs = langs;
        },
        setLoading(state, loading) {
            state.loading = loading;
        },
        setLangsCanceller(state, cancel) {
            state.langsCanceller = cancel;
        },
    },
    actions: {
        async fetchLangs({ commit, state }) {
            if (state.langsCanceller) {
                state.langsCanceller();
                commit("setLangsCanceller", undefined);
            }

            commit("setLoading", true);
            try {
                const url = `${window.NovaTranslations.url}/langs`;
                const data = await window.Nova.request().get(url, {
                    cancelToken: new CancelToken((canceller) => {
                        commit("setLangsCanceller", canceller);
                    }),
                });
                commit("setLangs", data.data);
            } catch (e) {
                throw e;
            } finally {
                commit("setLoading", false);
            }
        },
    },
};
