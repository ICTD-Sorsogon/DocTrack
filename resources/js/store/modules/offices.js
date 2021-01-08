const state = {
    offices: [],
}

const getters = {
    offices: state => state.offices,
    offices_loading: state => state.office_list_loading,
}

const actions = {
    async getOffices({ commit }) {
        const response = await axios.get('office_list');
        commit('GET_ALL_OFFICES', response.data);
    },
}

const mutations = {
    GET_ALL_OFFICES (state, offices) {
        state.offices = offices;
    },
    EDIT_OFFICE () {

    }
}

export default {
    state,
    getters,
    actions,
    mutations
};