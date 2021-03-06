const state = {
    offices: [],
    office_names: []
};

const getters = {
    offices: state => state.offices,
    offices_loading: state => state.office_list_loading,
    office_names_list: state => state.office_names
};

const actions = {
    async getOffices({ commit }) {
        const response = await axios.get("/api/office_list");
        commit("GET_ALL_OFFICES", response.data);
    },
    async createNewOffice({ commit }, form) {
        await axios
            .post("/api/add_new_office", form)
            .then(response => {
                let res = {
                    status: "success",
                    message: `${form.name} was successfully added!`
                };
                commit("SNACKBAR_STATUS", res);
            })
            .catch(error => {
                let res = {
                    status: "failed",
                    message:
                        "The server replied with an error! Please Contact your administrator."
                };
                commit("SNACKBAR_STATUS", res);
            });
    },
    async importNewOffice({ commit }, office_data) {
        await axios
            .post("/api/import_new_office", office_data)
            .then(response => {
                let res = {
                    status: "success",
                    message: `File was successfully uploaded!`
                };
                commit("SNACKBAR_STATUS", res);
            })
            .catch(error => {
                let res = {
                    status: "failed",
                    message:
                        "The server replied with an error! Please Contact your administrator."
                };
                commit("SNACKBAR_STATUS", res);
            });
    },
    async updateExistingOffice({ commit }, form) {
        await axios
            .post("/api/update_existing_office", form)
            .then(response => {
                let res = {
                    status: "success",
                    message: `${form.name} was successfully updated!`
                };
                commit("SNACKBAR_STATUS", res);
            })
            .catch(error => {
                let res = {
                    status: "failed",
                    message:
                        "The server replied with an error! Please Contact your administrator."
                };
                commit("SNACKBAR_STATUS", res);
            });
    },
    async deleteOffice({ commit }, id) {
        await axios
            .post(`/api/delete_office/${id}`)
            .then(response => {
                let res = {
                    status: "success",
                    message: `Office successfully deleted!`
                };
                commit("SNACKBAR_STATUS", res);
            })
            .catch(error => {
                let res = {
                    status: "failed",
                    message:
                        "The server replied with an error! Please Contact your administrator."
                };
                commit("SNACKBAR_STATUS", res);
            });
    },
    async getOfficeNameList({ commit }) {
        const response = await axios.get("api/list_office_names");
        commit("GET_LIST_OFFICE_NAMES", response.data);
    }
};

const mutations = {
    GET_ALL_OFFICES(state, offices) {
        state.offices = offices;
    },
    EDIT_OFFICE() {},
    GET_LIST_OFFICE_NAMES(state, office_list) {
        state.office_names = office_list;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
