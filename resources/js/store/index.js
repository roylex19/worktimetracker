import Vue from 'vue'
import Vuex from 'vuex'

import department from "./modules/department"
import user from "./modules/user"
import project from "./modules/project"
import record from "./modules/record"
import auth from "./modules/auth"
import tracker from "./modules/tracker"

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        department,
        user,
        project,
        record,
        auth,
        tracker,
    },
})
