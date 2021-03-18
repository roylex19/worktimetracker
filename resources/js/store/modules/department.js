export default {
    actions: {
        async getDepartmentsList(context){
            await axios.get('/api/departments/list')
                .then(response => {
                    context.commit('UPDATE_DEPARTMENTS_LIST', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async getDepartments(context, {page, title, sort}){
            await axios.get('/api/departments', {
                params: {
                    page,
                    title,
                    sort
                }
            })
                .then(response => {
                    context.commit('UPDATE_DEPARTMENTS', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async getDepartmentsViews(context, id){
            await axios.get('/api/departmentsViews/' + id)
                .then(response => {
                    context.commit('UPDATE_DEPARTMENTS_VIEWS', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async createDepartment(context, newRec) {
            await axios.post('/api/departments', {data: newRec})
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('CREATE_DEPARTMENT', response.data.record)
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
        async createDepartmentView(context, newRec) {
            await axios.post('/api/departmentsViews', newRec)
                .then(response => {
                    Vue.$toast.open({
                        message: response.data.message,
                        type: response.data.type,
                    })
                    $('#modalDepartmentsViewSetup').modal('hide')
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async updateDepartment(context, rec) {
            await axios.patch('/api/departments/' + rec.id, {data: {
                    title: rec.title,
                }})
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('UPDATE_DEPARTMENT', response.data.record)
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
        async updateDepartmentView(context, rec) {
            await axios.patch('/api/departmentsViews/' + rec.id, {data: {
                    title: rec.title,
                }})
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
        async deleteDepartment(context, id) {
            await axios.delete('/api/departments/' + id)
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('DELETE_DEPARTMENT', id)
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
    },
    mutations: {
        UPDATE_DEPARTMENTS(state, departments){
            state.departments = departments
        },
        UPDATE_DEPARTMENTS_VIEWS(state, departmentsViews){
            state.departmentsViews = departmentsViews
        },
        UPDATE_DEPARTMENTS_LIST(state, departments){
            state.departmentsList = departments
        },
        CREATE_DEPARTMENT(state, newRec){
            state.departments.data.unshift(Object.assign({}, newRec))
        },
        CREATE_DEPARTMENTS_VIEWS(state, newRec){
            state.departmentsViews.unshift(Object.assign({}, newRec))
        },
        UPDATE_DEPARTMENT(state, rec){
            const index = state.departments.data.findIndex(r => r.id === rec.id)
            Object.assign(state.departments.data[index], rec)
        },
        DELETE_DEPARTMENT(state, id){
            const index = state.departments.data.findIndex(rec => rec.id === id)
            state.departments.data.splice(index, 1)
        }
    },
    state: {
        departments: {},
        departmentsViews: [],
        departmentsList: []
    },
    getters: {
        allDepartments(state){
            return state.departments
        },
        allDepartmentsViews(state){
            return state.departmentsViews
        },
        departmentById: state => id => {
            return state.departments.data.find(rec => rec.id === id)
        },
        departmentsList(state) {
            return state.departmentsList
        },
        countDepartments(state){
            return state.departments.total
        },
        departmentsListNotMy: state => id => {
            return state.departmentsList.filter(rec => rec.id !== id)
        }
    },
}
