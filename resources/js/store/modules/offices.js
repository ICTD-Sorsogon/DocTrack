
import snackbar from './snackbars'

const state = {
    offices: [],
}

const getters = {
    offices: state => state.offices,
    offices_loading: state => state.office_list_loading,
    //form_requests: state => state.form_requests,
}

const actions = {
    async getOffices({ commit }) {
        const response = await axios.get('/api/office_list');
        commit('GET_ALL_OFFICES', response.data);
    },
    async createNewOffice({ commit }, form) {

        //console.log('snackbar stat:',snackbar.state.form_requests);

        await axios.post('/api/add_new_office', form)
        .then(response => {
            console.log('RESPONSE OK:', response);

            const data = {
                form_type: 'new_office',
                code: 'SUCCESS',
                message: `${form.office_code} successfully added!`,
                response_data: response.data
            }
            //commit('UPDATE_OFFICE_LIST', data);
            commit('snackbars/UPDATE_SNACKBAR_MESSAGE_STATUS', data, { root: true })
        })
        .catch(error => {
           // console.log('RESPONSE ERROR:',error);

         //console.log(error.response.data);

            const error_data = {
                form_type: 'new_office',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
               // message: `The server replied with an error! Please Contact your administrator\nException Type : ${error.response.data.exception}`,
            }
            //console.log('ERROR:' + error.response.data);
            commit('snackbars/THROW_SNACKBAR_SERVER_ERROR', error_data, { root: true })
            //commit('THROW_SERVER_ERROR', error_data)
        });


    },
    async updateExistingOffice({ commit }, form) {
        await axios.post('/api/update_existing_office', form)
        .then(response => {
            const data = {
                form_type: 'update_office',
                code: 'SUCCESS',
                message: `${form.office_code} successfully updated!`,
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
                message: `${id} successfully deleted!`,
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
    /*UPDATE_OFFICE_LIST(state, data, rootState) {
        console.log('root:' + rootState);

        // TODO: Update documents list and document tracking list
        state.form_requests.request_form_type = data.form_type;
        state.form_requests.request_status = data.code;
        state.form_requests.status_message = data.message;
        snackbar.form_requests.sample = 'gg'


    },
    THROW_SERVER_ERROR(state, error) {
        state.form_requests.request_form_type = error.form_type;
        state.form_requests.request_status = error.code;
        state.form_requests.status_message = error.message;
    },*/
}

export default {
    state,
    getters,
    actions,
    mutations
};