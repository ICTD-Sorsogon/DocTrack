const state = {
    snackbar: {
        showing: false,
        text: '',
        color: 'success',
        icon: 'mdi-checkbox-blank-circle',
    }
}

const getters = {
    snackbar: state => state.snackbar,
}

const actions = {
    setSnackbar({commit}, snackbar) {
        snackbar.color = snackbar.color || 'success';
        commit('SET_SNACKBAR', snackbar);
    },
    unsetSnackbar({ commit }) {
        commit('UNSET_SNACKBAR');
    }
}

const mutations = {
    SET_SNACKBAR(state, snackbar) {
        state.snackbar = snackbar;
    },
    UNSET_SNACKBAR(state) {
        state.snackbar.showing = false;
        state.snackbar.text = '';
        state.text = '',
        state.color = 'success';
        state.icon = 'mdi-checkbox-blank-circle';
    },
}

export default {
    state,
    getters,
    actions,
    mutations
};