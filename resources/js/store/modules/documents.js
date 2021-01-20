const state = {
    types: '',
    allDocuments: [],
    documents: [],
    document_types: [],
    document_loading: false,
    document_type_loading: false,
    selected_document: {},
    id: '',
}

const getters = {
    find_document: ({documents}) => (id) => documents.find(doc => doc.id == id),
    get_alldocument: state => state.allDocuments,
    documents: state => state.documents,
    document_types: state => state.document_types,
    selected_document: state => state.selected_document,
}

const actions = {
    async getDocument({commit}) {
        const response = await axios.get(`/api/tracking_list`);
        commit('GET_ALL_DOCUMENTS', response.data);
    },
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
    async getNonPaginatedActiveDocuments({ commit }) {
        const response = await axios.get(`/api/get_non_page_active_documents`);
        commit('GET_NON_PAGINATED_ACTIVE_DOCUMENTS', response.data);
    },
    async getDocumentTypes({ commit }) {
        const response = await axios.get('/api/document_type_list');
        commit('GET_ALL_DOCUMENT_TYPES', response.data);
    },
    async createNewDocument({ commit }, form) {
        await axios.post(`/api/add_new_document`, form)
        .then(response => {
            let res = {
                status: 'success',
                message: `Document ${form.tracking_id} created!`
            }
            commit('SNACKBAR_STATUS', res)
        })
        .catch(error => {
            let res = {
                status: 'failed',
                message: `The server replied with an error! Please Contact your administrator\nException Type : ${error.response.data.exception}`
            }
            commit('SNACKBAR_STATUS', res)
        });
    },
    async setDocument({ commit }, document) {
        commit('SET_SELECTED_DOCUMENT', document);
    },
    async unsetDocument({ commit }) {
        commit('UNSET_SELECTED_DOCUMENT');
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
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}