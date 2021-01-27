const state = {
    page_loader: true,
    datatable_loader: true,
    submenu_opened: false,
}

const getters = {
    page_loader: state => state.page_loader,
    datatable_loader: state => state.datatable_loader,
}

const actions = {
    setLoader({ commit }) {
        commit('SET_PAGE_LOADER');
    },
    unsetLoader({ commit }) {
        commit('UNSET_PAGE_LOADER');
    },
    setDataTableLoader({ commit }) {
        commit('SET_DATATABLE_LOADER');
    },
    unsetDataTableLoader({ commit }) {
        commit('UNSET_DATATABLE_LOADER');
    }
}

const mutations = {
    SET_PAGE_LOADER(state) {
        state.page_loader = true;
    },
    UNSET_PAGE_LOADER(state) {
        state.page_loader = false;
    },
    SET_DATATABLE_LOADER(state) {
        state.datatable_loader = true;
    },
    UNSET_DATATABLE_LOADER(state) {
        state.datatable_loader = false;
    },
    TOGGLE_SUBMENU(state, value) {
        state.submenu_opened = value;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
};