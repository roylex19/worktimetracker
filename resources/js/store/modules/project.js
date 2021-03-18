export default {
    actions: {
        async getProjects(context, {page, title, sort}){
            await axios.get('/api/projects', {
                params: {
                    page,
                    title,
                    sort
                }
            })
                .then(response => {
                    context.commit('UPDATE_PROJECTS', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async getProjectsList(context){
            await axios.get('/api/projects/list')
                .then(response => {
                    context.commit('UPDATE_PROJECTS_LIST', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async getProjectsListRecent(context){
            await axios.get('/api/projects/listRecent')
                .then(response => {
                    context.commit('UPDATE_PROJECTS_LIST', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async createProject(context, newRec) {
            await axios.post('/api/projects', {data: newRec})
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('CREATE_PROJECT', response.data.record)
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
        async updateProject(context, rec) {
            await axios.patch('/api/projects/' + rec.id, {data: {
                    title: rec.title,
                }})
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('UPDATE_PROJECT', response.data.record)
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
        async deleteProject(context, id) {
            await axios.delete('/api/projects/' + id)
                .then(response => {
                    if(response.data.type === 'success'){
                        context.commit('DELETE_PROJECT', id)
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
        UPDATE_PROJECTS(state, projects){
            state.projects = projects
        },
        UPDATE_PROJECTS_LIST(state, projects){
            state.projectsList = projects
        },
        CREATE_PROJECT(state, newRec){
            state.projects.data.unshift(Object.assign({}, newRec))
        },
        UPDATE_PROJECT(state, rec){
            const index = state.projects.data.findIndex(r => r.id === rec.id)
            Object.assign(state.projects.data[index], rec)
        },
        DELETE_PROJECT(state, id){
            const index = state.projects.data.findIndex(rec => rec.id === id)
            state.projects.data.splice(index, 1)
        }
    },
    state: {
        projects: {},
        projectsList: []
    },
    getters: {
        allProjects(state){
            return state.projects
        },
        projectsList(state){
            return state.projectsList
        },
        projectById: state => id => {
            return state.projects.data.find(rec => rec.id === id)
        },
        projectFromListByIndex: state => (index = 0) => {
            return state.projectsList[index]
        },
        allProjectsSort(state){
            return state.projects.sort((a, b) => {
                if (a.title > b.title) {
                    return 1;
                }
                if (a.title < b.title) {
                    return -1;
                }
                return 0;
            })
        },
        countProjects(state){
            return state.projects.total
        }
    },
}
