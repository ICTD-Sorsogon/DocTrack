const state = {
    documents: [],
    document_types: [],
    /*form_requests : {
        request_form_type: '',
        request_status: '',
        status_message: '',
    },*/
    document_loading: false,
    document_type_loading: false,
    selected_document: {},
    id: '',
}

const getters = {
    documents: ({documents}) => {
        for( let doc of documents){
            console.log(doc.is_external)
            doc.is_external = doc.is_external ? 'External' : 'Internal'
            doc.is_terminal = doc.is_terminal ? 'Yes' : 'No'
        }
        return documents
    },
    document_types: state => state.document_types,
    //form_requests: state => state.form_requests,
    selected_document: state => state.selected_document,
    getDocument: ({documents})=> (id) =>{
        return documents.data.filter(item=>
            item.id == id
            );
    }
}

const actions = {
    async updateDocument({commit}, form) {
        commit('UPDATE_DOCUMENT', form);
    },
    async setImmediate({commit}, id) {
        commit('SET_ID_DOCUMENT', id);
    },
    async getActiveDocuments({ commit }, page_number) {
        const response = await axios.get(`/api/get_active_documents?page=${page_number}`);
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
        await axios.post('/api/add_new_document', form)
        .then(response => {
            const data = {
                form_type: form.form_type,
                code: 'SUCCESS',
                message: `Document ${form.tracking_id} created!`,
                response_data: response.data
            }
            commit('UPDATE_DOCUMENT_LIST', data);
        })
        .catch(error => {
            const error_data = {
                form_type: form.form_type,
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator\nException Type : ${error.response.data.exception}`,
            }
            commit('THROW_SERVER_ERROR', error_data)
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
    UPDATE_DOCUMENT(state, form){
        let document = state.documents.data.filter(item =>{
                if (item.tracking_id == form.tracking_code) {
                    item.subject = form.document_title;
                }
            }
        );

            console.log(state.documents);

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
    UPDATE_DOCUMENT_LIST(state, data) {
        // TODO: Update documents list and document tracking list
        state.form_requests.request_form_type = data.form_type;
        state.form_requests.request_status = data.code;
        state.form_requests.status_message = data.message;
    },
    THROW_SERVER_ERROR(state, error) {
        state.form_requests.request_form_type = error.form_type;
        state.form_requests.request_status = error.code;
        state.form_requests.status_message = error.message;
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