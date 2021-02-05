import axios from "axios";

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

}

const getters = {
    find_document: ({documents}) => (id) => documents.find(doc => doc.id == id),
    get_alldocument: state => state.allDocuments,
    documents: state => state.documents,
    documentsArchive: state => state.documentsArchive,
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
    getArchiveDocuments({ commit }, filter) {

        /*mutateStateStatus:             response.mutateStateStatus = 'createstate || updatestate'
        getDataFrom:                   response.getDataFrom = "backup || db || none"

        filterBy:                      response.filterBy = "By Date Range || By Year"
        filterSelectedYear:            response.filterSelectedYear = "[array]"
        filterSelectedDateRange:       response.filterSelectedDateRange = "[array]"
        data:                          [...response.data] = '[array]'
        backup:                        {
                                            filterBy: '',
                                            filterSelected: [],
                                            data: []
                                       }*/

        /*const filterSelectedAndResponse = {
            //mutateStateStatus: filter.mutateStateStatus,
            //getDataFrom: filter.getDataFrom,
            filterBy: filter.filterBy,
            filterSelectedYear: filter.filterSelectedYear,
            filterSelectedDateRange: filter.filterSelectedDateRange,
            data: [...filter.data],
            backup: {
                filterBy: filter.backup.filterBy,
                filterSelected: response.backup.filterSelected,
                data: [...response.backup.data]
            }
        }*/
        console.log('filter here');
        console.log(filter);

        const filterSelectedAndResponse = {
            backup: {}
        };

        switch (filter.getDataFrom) {
            case 'backup':
                console.log('end');
                    filterSelectedAndResponse.mutateStateStatus = filter.mutateStateStatus
                    filterSelectedAndResponse.getDataFrom = filter.getDataFrom

                    filterSelectedAndResponse.filterBy = filter.filterBy
                    filterSelectedAndResponse.filterSelectedYear = filter.filterSelectedYear
                    filterSelectedAndResponse.filterSelectedDateRange = filter.filterSelectedDateRange
                    filterSelectedAndResponse.filterSelectedYear = filter.filterSelectedYear
                    filterSelectedAndResponse.data = [...filter.data]

                    filterSelectedAndResponse.backup.filterSelected = filter.backup.filterSelectedDateRange
                    filterSelectedAndResponse.backup.filterBy = filter.backup.filterSelectedYear
                    filterSelectedAndResponse.backup.data = [...filter.backup.data]
                    commit('GET_ALL_ARCHIVE_DOCUMENTS', filterSelectedAndResponse);
                break;
            case 'db':
                //console.log((filter.filterBy == 'By Date Range')? { selected: filter.filterSelectedDateRange } : { selected: filter.filterSelectedYear })
                    axios.post(`/api/get_archive_documents`, (
                        (filter.filterBy == 'By Date Range')?
                            {selected: filter.filterSelectedDateRange, filterBy: 'Date'} :
                            {selected: filter.filterSelectedYear, filterBy: 'Year'}
                    )).then(response => {
                        filterSelectedAndResponse.mutateStateStatus = filter.mutateStateStatus
                        filterSelectedAndResponse.getDataFrom = filter.getDataFrom

                        filterSelectedAndResponse.filterBy = filter.filterBy
                        filterSelectedAndResponse.data = response.data
                        filterSelectedAndResponse.backup.filterBy = filter.backup.filterBy
                        filterSelectedAndResponse.backup.data = response.data
                        if (filter.filterBy == 'By Date Range') {
                            console.log('range');
                            console.log( filter.backup.filterBy)
                            filterSelectedAndResponse.filterSelectedDateRange = filter.filterSelectedDateRange
                            filterSelectedAndResponse.backup.filterSelected = filter.backup.filterSelected
                        } else {
                            filterSelectedAndResponse.filterSelectedYear = filter.filterSelectedYear
                            filterSelectedAndResponse.backup.filterSelected = filter.backup.filterSelected
                        }
                        console.log(filterSelectedAndResponse);
                        commit('GET_ALL_ARCHIVE_DOCUMENTS', filterSelectedAndResponse);
                    })
                break;
        }

        //commit('GET_ALL_ARCHIVE_DOCUMENTS', filterSelectedAndResponse);


        //console.log(response)
        /*const filterSelectedAndResponse = {
            filterBy: filter.filterBy,
            filterSelected: filter.filterSelected,
            data: response.data
        }*/

        console.log('---document dispatch payload')
        console.log(filterSelectedAndResponse);
        console.log('---')



        //commit('GET_ALL_ARCHIVE_DOCUMENTS', response.data);
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
    async documentReports({ commit }) {
        const response = await axios.get('/api/tracking_reports')
        .then(response => {
            commit('GET_TRACKING_REPORTS', response.data);
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
    GET_ALL_ARCHIVE_DOCUMENTS(state, response) {

        console.log('MUTATE SUCCESS')

        //state.documentsArchive = []


        //state.documentsArchive = [];
        //state.documentsArchive.data.splice(0, state.documentsArchive.data.length, ...response);


        /*mutateStateStatus:             response.mutateStateStatus = 'createstate || updatestate'
        getDataFrom:                   response.getDataFrom = "backup || db || none"

        filterBy:                      response.filterBy = "By Date Range || By Year"
        filterSelectedYear:            response.filterSelectedYear = "[array]"
        filterSelectedDateRange:       response.filterSelectedDateRange = "[array]"
        data:                          [...response.data] = '[array]'
        backup:                        {
                                            filterBy: '',
                                            filterSelected: [],
                                            data: []
                                       }*/


        if (response.mutateStateStatus == 'createstate') {
            state.documentsArchive = []
            state.documentsArchive.push({
                //mutateStateStatus: response.mutateStateStatus,
                //getDataFrom: response.getDataFrom,
                filterBy: 'By Date Range',
                filterSelectedYear: [new Date().getFullYear().toString()],
                filterSelectedDateRange: [
                    new Date().toISOString().substr(0, 10),
                    new Date().toISOString().substr(0, 10)
                ],
                data: [...response.data],
                backup: {
                    filterBy: 'By Date Range',
                    filterSelected: [
                        new Date().toISOString().substr(0, 10),
                        new Date().toISOString().substr(0, 10)
                    ],
                    data: [...response.backup.data]
                }
            })
        } else {
            if (response.getDataFrom == 'backup') {
                console.log('dd')
                //state.documentsArchive = []
                state.documentsArchive[0].filterBy = response.filterBy
                if (response.filterBy == "By Date Range") {
                    state.documentsArchive[0].filterSelectedDateRange = response.filterSelectedDateRange
                } else {
                    state.documentsArchive[0].filterSelectedYear = response.filterSelectedYear
                }

            } else {
                // console.log('end mutate:', state.documentsArchive[0].backup.filterBy)
                //state.documentsArchive.mutateStateStatus = response.mutateStateStatus
                //state.documentsArchive.getDataFrom = response.getDataFrom


                state.documentsArchive[0].filterBy = response.filterBy

                if (response.filterBy == 'By Date Range') {
                    state.documentsArchive[0].filterSelectedYear = state.documentsArchive[0].filterSelectedYear
                    state.documentsArchive[0].filterSelectedDateRange = response.filterSelectedDateRange
                } else {
                    state.documentsArchive[0].filterSelectedDateRange = state.documentsArchive[0].filterSelectedDateRange
                    state.documentsArchive[0].filterSelectedYear = response.filterSelectedYear
                }

                state.documentsArchive[0].data = [...response.data]

                state.documentsArchive[0].backup.filterBy = response.backup.filterBy
                state.documentsArchive[0].backup.filterSelected = response.backup.filterSelected
                state.documentsArchive[0].backup.data = [...response.backup.data]
            }


        }




        /*state.documentsArchive = []
        state.documentsArchive.push({
            filter: response.filterBy,
            filter_selected: response.filterSelected,
            data: [...response.data]
        });*/

        //state.documentsArchive[0].filter = "By Date Range1"
        //console.log('=' + state.documentsArchive[0].filter)



        /*state.documentsArchive = []
        state.documentsArchive.push({
            filter: 'By Date Range',
            filter_selected: [
                new Date().toISOString().substr(0, 10),
                new Date().toISOString().substr(0, 10)
            ],
            data: [...response]
        });*/

       /*state.documentsArchive.data = response;
       state.documentsArchive.filter = 'By Date Range';
       state.documentsArchive.filter_selected = {
           date1: new Date().toISOString().substr(0, 10),
           date2: new Date().toISOString().substr(0, 10)
       };*/
       //debugger

       //console.log(...state.documentsArchive)

       //console.log(state.documentsArchive);
      // console.log(state.documentsArchive.data);
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
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}