import Dashboard from './components/user/Dashboard';
import DocumentRecords from './components/user/DocumentRecords';
import AccountSettings from './components/user/AccountSettings';
import UserManagement from './components/user/UserManagement';

import AllDocument from './components/user/components/AllDocument'
import NewDocument from './components/user/components/NewDocument';
import EditDocument from './components/user/components/EditDocument';
import ReceiveDocument from './components/user/components/ReceiveDocument'
import DocumentAction from './components/user/DocumentAction';
import ReportAging from './components/user/ReportAging';
import ReportLog from './components/user/ReportLog';
import ReportMasterList from './components/user/ReportMasterList';
import ReportOfficeList from './components/user/ReportOfficeList';

import Welcome from './components/Welcome'

import Login from './components/Login';
import HomeContainer from './components/HomeContainer';
import NotFound from './components/NotFound';
import axios from 'axios';

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            component: Welcome,
            children: [
                {
                    path: '/login',
                    component: Login,
                    name: 'Login',
                    beforeEnter: (to, from, next) => {
                        axios.get('api/authenticated').then((response) => {
                            console.log('Login Passed:', response.data);
                            next();
                        }).catch((error) => {
                            console.log('Login Failed: ', error);
                            next()
                        });
                    },
                },
                {
                    path: '',
                    component: HomeContainer,
                    requiresAuth: true,
                    redirect: '/all_active_document',
                    beforeEnter: (to, from, next) => {
                        axios.get('api/authenticated').then((response) => {
                            console.log('Auth Passed: ', response);
                            next()
                        }).catch((error) => {
                            console.log('Auth Failed: ', error);
                            next({ name: 'Login' });
                        });
                    },
                    children: [
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
                            path: 'edit_document/:type/:id?',
                            component: EditDocument,
                            name: 'Edit Document'
                        },
                        {
                            path: 'all_active_document',
                            component: AllDocument,
                            name: 'All Active Documents',
                        },
                        {
                            path: 'receive_document/:type/:id?',
                            component: ReceiveDocument,
                            name: 'Receive Document',
                        },
                        {
                            path: 'forward_document',
                            component: DocumentAction,
                            name: 'Forward Document',
                            props: true,
                        },
                        {
                            path: 'terminal_document',
                            component: DocumentAction,
                            name: 'Terminal Document'
                        },
                        {
                            path: 'reports/aging',
                            component: ReportAging,
                            name: 'Document Aging Report'
                        },
                        {
                            path: 'reports/logs',
                            component: ReportLog,
                            name: 'Log Report'
                        },
                        {
                            path: 'reports/master_list',
                            component: ReportMasterList,
                            name: 'Document Master List'
                        },
                        {
                            path: 'reports/office_list',
                            component: ReportOfficeList,
                            name: 'Office List'
                        },
                        {
                            path: 'user_management',
                            component: UserManagement,
                            name: 'User Management'
                        }
                    ]
                },

            ],
        },
        {
            path: '*',
            component: NotFound,
            name: 'Not Found'
        },
        // FIXME: fix route guards


    ]
}