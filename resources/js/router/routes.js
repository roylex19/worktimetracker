import Welcome from "../views/Welcome";

import Departments from "../views/Departments";
import Users from "../views/Users";
import Records from "../views/Records";
import MyRecords from "../views/MyRecords";
import Projects from "../views/Projects";
import SignIn from "../views/SignIn";
import Register from "../views/Register";
import UserPage from "../views/UserPage";

import store from '../store'

const routes = [
    {
        path: '/',
        name: 'home',
        component: SignIn,
        beforeEnter: (to, from, next) => {
            if(store.getters['auth/auth']){
                return next({
                    name: 'myRecords'
                })
            }

            next()
        }
    },
    {
        path: '/signIn',
        name: 'signIn',
        component: SignIn,
        beforeEnter: (to, from, next) => {
            if(store.getters['auth/auth']){
                return next({
                    name: 'myRecords'
                })
            }

            next()
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        beforeEnter: (to, from, next) => {
            if(store.getters['auth/auth']){
                return next(false)
            }
            next()
        }
    },
    {
        path: '/departments',
        name: 'departments',
        component: Departments,
        beforeEnter: (to, from, next) => {
            if(!store.getters['auth/auth']){
                return next({
                    name: 'signIn'
                })
            }

            if(!store.getters['auth/user'].admin){
                return next(false)
            }

            next()
        }
    },
    {
        path: '/users',
        name: 'users',
        component: Users,
        beforeEnter: (to, from, next) => {
            if(!store.getters['auth/auth']){
                return next({
                    name: 'signIn'
                })
            }

            if(!store.getters['auth/user'].admin){
                return next(false)
            }

            next()
        },
    },
    {
        path: '/projects',
        name: 'projects',
        component: Projects,
        beforeEnter: (to, from, next) => {
            if(!store.getters['auth/auth']){
                return next({
                    name: 'signIn'
                })
            }

            if(!store.getters['auth/user'].admin){
                return next(false)
            }

            next()
        }
    },
    {
        path: '/records',
        name: 'records',
        component: Records,
        beforeEnter: (to, from, next) => {
            if(!store.getters['auth/auth']){
                return next({
                    name: 'signIn'
                })
            }

            if(!store.getters['auth/user'].admin){
                return next(false)
            }

            next()
        }
    },
    {
        path: '/myRecords',
        name: 'myRecords',
        component: MyRecords,
        beforeEnter: (to, from, next) => {
            if(!store.getters['auth/auth']){
                return next({
                    name: 'signIn'
                })
            }
            next()
        },
    },
    {
        path: '/personal',
        name: 'userPage',
        component: UserPage,
        beforeEnter: (to, from, next) => {
            if(!store.getters['auth/auth']){
                return next({
                    name: 'signIn'
                })
            }
            next()
        },
    },
];

export default routes
