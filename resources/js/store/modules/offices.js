const state = {
    offices: [],
}

const getters = {
    offices: state => state.offices,
    offices_loading: state => state.office_list_loading,
}

const actions = {
    async getOffices({ commit }) {
        const response = await axios.get('/api/office_list');
        commit('GET_ALL_OFFICES', response.data);
    },
    async createNewOffice({ commit }, form) {
        await axios.post('/api/add_new_office', form)
        .then(response => {
            const data = {
                form_type: 'new_office',
                code: 'SUCCESS',
                message: `${form.name} was successfully added!`,
                response_data: response.data
            }
            commit('snackbars/UPDATE_SNACKBAR_MESSAGE_STATUS', data, { root: true })
        })
        .catch(error => {
            const error_data = {
                form_type: 'new_office',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('snackbars/THROW_SNACKBAR_SERVER_ERROR', error_data, { root: true })
        });
    },
    async updateExistingOffice({ commit }, form) {
        await axios.post('/api/update_existing_office', form)
        .then(response => {
            const data = {
                form_type: 'update_office',
                code: 'SUCCESS',
                message: `${form.name} was successfully updated!`,
                response_data: response.data
            }
            commit('snackbars/UPDATE_SNACKBAR_MESSAGE_STATUS', data, { root: true })
        })
        .catch(error => {
            const error_data = {
                form_type: 'update_office',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('snackbars/THROW_SNACKBAR_SERVER_ERROR', error_data, { root: true })
        });
    },
    async deleteOffice({ commit }, id) {
        await axios.post(`/api/delete_office/${id}`)
        .then(response => {
            const data = {
                form_type: 'delete_office',
                code: 'SUCCESS',
                message: `${response.data[0].name} \nwas successfully deleted!`,
                response_data: response.data
            }
            commit('snackbars/UPDATE_SNACKBAR_MESSAGE_STATUS', data, { root: true })
        })
        .catch(error => {
            console.log(error);
            const error_data = {
                form_type: 'delete_office',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('snackbars/THROW_SNACKBAR_SERVER_ERROR', error_data, { root: true })
        });
    },
}

const mutations = {
    GET_ALL_OFFICES (state, offices) {
        state.offices = offices;
    },
    EDIT_OFFICE () {
    },
}

export default {
    state,
    getters,
    actions,
    mutations
};