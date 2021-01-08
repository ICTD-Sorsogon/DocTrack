import Dashboard from './components/user/Dashboard';
import DocumentRecords from './components/user/DocumentRecords';
import AccountSettings from './components/user/AccountSettings';

import AllDocument from './components/user/components/AllDocument'
import NewDocument from './components/user/components/NewDocument';
import DocumentAction from './components/user/DocumentAction';
import ReportAging from './components/user/ReportAging';
import ReportMasterList from './components/user/ReportMasterList';

import Login from './components/Login';
import HomeContainer from './components/HomeContainer';
import NotFound from './components/NotFound';

// TODO: Fix routes structure
export default {
    base: '/',
    mode: 'history',
    routes: [
        {
            path: '*',
            component: NotFound,
            name: 'Not Found'
        },
        {
            path: '/',
            component: Login,
            name: 'Login',
        },
        {
            path: '/',
            component: HomeContainer,
            beforeEnter: (to, from, next) => {
                axios.get('/authenticated').then((response) => {
                    next()
                }).catch(() => {
                    return next({ name: 'Login'})
                });
            },
            children: [
                {
                    path: 'dashboard',
                    component: Dashboard,
                    name: 'Dashboard',
                },
                {
                    path: 'document_records',
                    component: DocumentRecords,
                    name: 'Document Records',
                },
                {
                    path: 'account_settings',
                    component: AccountSettings,
                    name: 'Account Settings'
                },
                {
                    path: 'new_document',
                    component: NewDocument,
                    name: 'New Document'
                },
                {
                    path: 'all_active_document',
                    component: AllDocument,
                    name: 'All Active Documents',
                },
                // {
                //     path: 'document_details/:id',
                //     component: DocumentDetails,
                //     name: 'Document Details',
                //     beforeEnter: (to, from, next) => {
                //         if (to.params.id) {
                //             axios.get(`get_document_details/${to.params.id}`).then((response) => {
                //                 next()
                //             }).catch(() => {
                //                 return next('All Active Documents');
                //             });
                //         }else {
                //             return next(
                //                 vm => {
                //                     vm.prevRoute = from;
                //                 }
                //             );
                //         }
                //     },
                // },
                {
                    path: 'receive_document/:routing_number',
                    component: DocumentAction,
                    name: 'Receive Document'
                },
                {
                    path: 'forward_document/:routing_number',
                    component: DocumentAction,
                    name: 'Forward Document'
                },
                {
                    path: 'terminal_document/:routing_number',
                    component: DocumentAction,
                    name: 'Terminal Document'
                },
                {
                    path: 'reports/aging',
                    component: ReportAging,
                    name: 'Document Aging Report'
                },
                {
                    path: 'reports/master_list',
                    component: ReportMasterList,
                    name: 'Document Master List'
                }
            ]
        },

    ]
}