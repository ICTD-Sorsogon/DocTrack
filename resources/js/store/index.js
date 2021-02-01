import documents from './modules/documents';
import users from './modules/users';
import snackbars from './modules/snackbars';
import loader from './modules/loader';
import offices from './modules/offices';
import createPersistedState from 'vuex-persistedstate'

export default {
    modules: {
        strict: process.env.NODE_ENV !== 'production',
        snackbars,
        users,
        documents,
        offices,
        loader,
    },
    plugins: [createPersistedState({
        key: 'keyname',
        storage: window.sessionStorage,
    })],
};