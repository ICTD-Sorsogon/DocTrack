import axios from "axios";
import { constant } from "lodash";

const state = {
    types: '',
    allDocuments: [],
    documents: [],
    documentsArchive: [],
    document_types: [],
    document_loading: false,
    document_type_loading: false,
    selected_document: {},
    id: '',
    tracking_reports: [],
    office_reports: []

}

const getters = {
    find_document: ({documents}) => (id) => documents.find(doc => doc.id == id),
    get_alldocument: state => state.allDocuments,
    documents: state => state.documents,
    documentsArchive: state => state.documentsArchive,
    document_types: state => state.document_types,
    selected_document: state => state.selected_document,
    office_reports_get: state => state.office_reports
}

const actions = {
    async updateDocument({commit}, form) {
        commit('UPDATE_DOCUMENT', form);
    },
    async setImmediate({commit}, id) {
        commit('SET_ID_DOCUMENT', id);
    },
    async getActiveDocuments({ commit }, page_number) {
        const response = await axios.get(`/api/get_active_documents`);
        commit('GET_ALL_ACTIVE_DOCUMENTS', response.data);
    },
    async getArchiveDocuments({ commit }, filter) {
        const response = await axios.post(`/api/get_archive_documents`, (
            (filter.filterBy == 'Year')? {selected: filter.year.list, filterBy: 'Year'} : {selected: filter.date.list, filterBy: 'Date'}
        ))
        if (response?.data?.data != undefined) {
            const data = response.data.data
            filter.yearFromDb = response.data.year
            filter.hasNewTerminated = false
            await commit('GET_ALL_ARCHIVE_DOCUMENTS', {...filter, data})
        } else {
            commit('SET_SNACKBAR', { showing: true, title: 'FAILED', text: 'Error fetching data', color: '#F45448', icon : 'mdi-close-thick' })
        }
    },
    async getNonPaginatedActiveDocuments({ commit }) {
        const response = await axios.get(`/api/get_non_page_active_documents`);
        commit('GET_NON_PAGINATED_ACTIVE_DOCUMENTS', response.data);
    },
    async getDocumentTypes({ commit }) {
        const response = await axios.get('/api/document_type_list');
        commit('GET_ALL_DOCUMENT_TYPES', response.data);
    },
    async createNewDocument({ commit, dispatch }, form) {
        await axios.post(`/api/add_new_document/${form.id ?? ''}`, form)
        .then(response => {
            let res = {
                status: 'success',
                message: `Document ${response.data.tracking_code} created!`
            }
            commit('SNACKBAR_STATUS', res)
            dispatch('getActiveDocuments')
        })
        .catch(error => {
            let res = {
                status: 'failed',
                message: `The server replied with an error! Please Contact your administrator\nException Type : ${error.response.data.exception}`
            }
            commit('SNACKBAR_STATUS', res)
        });
    },
    async receiveDocumentConfirm({ commit }, form) {
        await axios.post(`/api/receive_document_confirm/${form.id}`, form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.subject} was successfully received!`,
            }
            commit('SNACKBAR_STATUS', data)

        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async forwardDocumentConfirm({ commit }, form) {
        await axios.post(`/api/forward_document_confirm/${form.id}`, form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.subject} was successfully forwarded!`,
            }
            commit('SNACKBAR_STATUS', data)

        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async terminateDocumentConfirm({ commit }, form) {
        await axios.post(`/api/terminate_document_confirm/${form.id}`, form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.subject} was successfully terminated!`,
            }
            commit('SNACKBAR_STATUS', data)
            commit('GET_ALL_ARCHIVE_DOCUMENTS', { action: 'update', hasNewTerminated: true })
        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async acknowledgeDocumentConfirm({ commit }, form) {
        await axios.post(`/api/acknowledge_document_confirm/${form.id}`, form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.subject} was successfully acknowledged!`,
            }
            commit('SNACKBAR_STATUS', data)

        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async holdRejectDocumentConfirm({ commit }, form) {
        await axios.post(`/api/hold_reject_document_confirm/${form.id}`, form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.subject} was ${form.hold_reject}!`,
            }
            commit('SNACKBAR_STATUS', data)

        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async changeDateDocumentConfirm({ commit }, form) {
        await axios.post(`/api/change_date_document_confirm/${form.id}`, form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.subject} was updated!`,
            }
            commit('SNACKBAR_STATUS', data)

        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async documentReports({ commit }) {
        const response = await axios.get('/api/tracking_reports')
        commit('GET_TRACKING_REPORTS', response.data);
    },
    async setDocument({ commit }, document) {
        commit('SET_SELECTED_DOCUMENT', document);
    },
    async unsetDocument({ commit }) {
        commit('UNSET_SELECTED_DOCUMENT');
    },
    async officeReports({ commit }) {
        const response = await axios.get('/api/office_reports')
        commit('GET_OFFICE_REPORTS', response.data);
    }
}

const mutations = {
    SET_TYPES(state, types){
        state.types = types;
    },
    GET_ALL_DOCUMENTS(state, response) {
        state.allDocuments = response
    },
    UPDATE_DOCUMENT(state, form){
        let document = state.documents.data.filter(item =>{
                if (item.tracking_id == form.tracking_code) {
                    item.subject = form.document_title;
                }
            }
        );

    },
    SET_ID_DOCUMENT(state, id){
        state.id = id;
    },
    GET_ALL_ACTIVE_DOCUMENTS(state, response) {
        state.documents = response;
    },
    GET_ALL_ARCHIVE_DOCUMENTS(state, response) {
        async function newState(mutate, filter) {
            state.documentsArchive = []
            await state.documentsArchive.push({
                year: filter.yearFromDb.map(String) || [],
                selected: {
                    filter: 'Date',
                    date: {
                        text: 'Date',
                        list: [ new Date().toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ],
                        data: filter.data || []
                    },
                    year: { text: 'Year', list: [], data: [] }
                },
                hasNewTerminated: (mutate == 'newTerminated')? true:false
            })
        }
        if (response.action == "new") {
            newState('new', response)
        } else {
            if (response.hasNewTerminated) {
                if (state.documentsArchive.length) {
                    state.documentsArchive[0].hasNewTerminated = true
                } else {
                    newState('newTerminated', {yearFromDb: [new Date().getFullYear()], data: []})
                }
            } else {
                state.documentsArchive[0].year = response.yearFromDb.map(String)
                state.documentsArchive[0].hasNewTerminated = false
                if (response.filterBy == 'Year') {
                    state.documentsArchive[0].selected.filter = response.filterBy
                    state.documentsArchive[0].selected.year.text = response.filterBy
                    state.documentsArchive[0].selected.year.list = response.year.list
                    state.documentsArchive[0].selected.year.data = response.data
                } else {
                    state.documentsArchive[0].selected.filter = response.filterBy
                    state.documentsArchive[0].selected.date.text = response.filterBy
                    state.documentsArchive[0].selected.date.list = response.date.list
                    state.documentsArchive[0].selected.date.data = response.data
                }
            }
        }
        //state.documentsArchive = []
    },
    RESET_ARCHIVE_STATE(state) {
        state.documentsArchive = []
    },
    SET_CURRENT_PAGE(state, data) {
        state.documents.current_page = data;
    },
    GET_NON_PAGINATED_ACTIVE_DOCUMENTS(state, response) {
        state.documents = response;
    },
    GET_ALL_DOCUMENT_TYPES(state, document_types) {
        state.document_types = document_types;
    },
    SET_SELECTED_DOCUMENT(state, document) {
        state.selected_document = document;
    },
    UNSET_SELECTED_DOCUMENT(state) {
        state.selected_document = {};
    },
    GET_TRACKING_REPORTS(state, reports) {
        state.tracking_reports = reports;
    },
    GET_OFFICE_REPORTS(state, reports) {
        state.office_reports = reports;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}