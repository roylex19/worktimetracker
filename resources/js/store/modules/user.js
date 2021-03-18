export default {
    actions: {
        async getUsersList(context){
            await axios.get('/api/users/list')
                .then(response => {
                    context.commit('UPDATE_USERS_LIST', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async getUsers(context, {page, name, department, phone, sort}){
            await axios.get('/api/users', {
                params: {
                    page,
                    name,
                    department,
                    phone,
                    sort
                }
            })
                .then(response => {
                    context.commit('UPDATE_USERS', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async createUser(context, newRec) {
            await axios.post('/api/users', {data: newRec})
                .then(response => {
                    if (response.data.type === 'success') {
                        context.commit('CREATE_USER', response.data.record)
                        $('#modalCreate').modal('hide')
                    }
                    Vue.$toast.open({
                        message: response.data.message,
                        type: response.data.type,
                    })
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async updateUser(context, rec) {
            await axios.patch('/api/users/' + rec.id, {data: {
                    name: rec.name,
                    email: rec.email,
                    phone: rec.phone,
                    department: rec.department,
                    disabled: rec.disabled
                }})
                .then(response => {
                    if (response.data.type === 'success') {
                        context.commit('UPDATE_USER', response.data.record)
                        $('#modalUpdate').modal('hide')
                    }
                    Vue.$toast.open({
                        message: response.data.message,
                        type: response.data.type,
                    })
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async deleteUser(context, id) {
            await axios.delete('/api/users/' + id)
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('DELETE_USER', id)
                    }
                    Vue.$toast.open({
                        message: response.data.message,
                        type: response.data.type,
                    })
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })

        },
        async updatePassword(context, data) {
            await axios.patch('/api/users/' + data.id + '/password', {
                password: data.password
            })
                .then(response => {
                    if(response.data.type === 'success'){
                        $('#modalPasswordSetup').modal('hide')
                    }
                    Vue.$toast.open({
                        message: response.data.message,
                        type: response.data.type,
                    })
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async updateAdminRule(context, {id, admin}) {
            await axios.patch('/api/users/' + id + '/admin', {
                admin
            })
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('UPDATE_ADMIN_RULES', {
                            id,
                            admin: response.data.admin,
                        })
                        $('#modalAdminSetup').modal('hide')
                    }
                    Vue.$toast.open({
                        message: response.data.message,
                        type: response.data.type,
                    })
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async deactivateUser(context, id) {
            await axios.patch('/api/users/' + id + '/deactivate')
                .then(response => {
                    Vue.$toast.open({
                        message: response.data.message,
                        type: response.data.type,
                    })
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async getUserHours({ commit }, { userId, month, year }) {
            await axios.get('/api/users/' + userId + '/hours', {
                params: {
                    month,
                    year
                }
            })
                .then(response => {
                    commit('UPDATE_USER_HOURS', response.data)
                })
        },
    },
    mutations: {
        UPDATE_USERS(state, users){
            state.users = users
        },
        UPDATE_USERS_LIST(state, users){
            state.usersList = users
        },
        CREATE_USER(state, newRec){
            state.users.data.unshift(Object.assign({}, newRec))
        },
        UPDATE_USER(state, rec){
            const index = state.users.data.findIndex(r => r.id === rec.id)
            Object.assign(state.users.data[index], rec)
        },
        DELETE_USER(state, id){
            const index = state.users.data.findIndex(rec => rec.id === id)
            state.users.data.splice(index, 1)
        },
        UPDATE_ADMIN_RULES(state, data){
            const index = state.users.data.findIndex(r => r.id === data.id)
            state.users.data[index].is_admin = data.admin
        },
        UPDATE_USER_HOURS(state, data){
            state.userHours = data
        }
    },
    state: {
        usersList: [],
        users: {},
        userHours: {}
    },
    getters: {
        allUsers(state){
            return state.users
        },
        allUsersList(state){
            return state.usersList
        },
        userById: state => id => {
            return state.usersList.find(rec => rec.id === id)
        },
        allUsersNotMe: state => id => {
            return state.usersList.filter(rec => rec.id !== id)
        },
        countUsers: state => {
            return state.users.total
        },
        userHours(state){
            return state.userHours
        }
    },
}
