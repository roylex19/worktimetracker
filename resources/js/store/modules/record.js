export default {
    actions: {
        async getRecords({commit}, {page, name, project, date, description, created_at, updated_at, hours, sort}){
            commit('UPDATE_RECORDS', [])
            await axios.get('/api/records', {
                params: {
                    page,
                    name,
                    project,
                    hours,
                    date,
                    description,
                    created_at,
                    updated_at,
                    sort
                },
            })
                .then(response => {
                    commit('UPDATE_RECORDS', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async getRecordsOfDepartments(context, {id, name, project, date, description, hours, created_at, updated_at, page, sort}){
            await axios.get('/api/records/' + id, {
                params: {
                    page,
                    name,
                    project,
                    hours,
                    date,
                    description,
                    created_at,
                    updated_at,
                    sort
                }
            })
                .then(response => {
                    context.commit('UPDATE_RECORDS', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        },
        async createRecord(context, newRec) {
            await axios.post('/api/records', {data: newRec})
                .then(response => {
                    if (response.data.type === 'success') {
                        for (let i = 0; i < response.data.records.length; i++){
                            //response.data.records[i].date = moment(response.data.records[i].date, 'YYYY-MM-DD').format('DD.MM.YYYY')
                            context.commit('CREATE_RECORD', response.data.records[i])
                        }

                        context.dispatch("getTrackerData")

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
        async updateRecord(context, rec) {
            await axios.patch('/api/records/' + rec.id, {data: rec})
                .then(response => {
                    if (response.data.type === 'success') {
                        //rec.date = moment(rec.date, 'YYYY-MM-DD').format('DD.MM.YYYY')
                        context.commit('UPDATE_RECORD', response.data.record)

                        $('#modalUpdate').modal('hide')

                        context.dispatch("getTrackerData")
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
        async deleteRecord(context, id) {
            await axios.delete('/api/records/' + id)
                .then(response => {
                    if (response.data.type === 'success') {
                        context.commit('DELETE_RECORD', id)

                        context.dispatch("getTrackerData")
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
        UPDATE_RECORDS(state, records){
            state.records = records
        },
        CREATE_RECORD(state, newRec){
            state.records.data.unshift(Object.assign({}, newRec))
        },
        UPDATE_RECORD(state, rec){
            const index = state.records.data.findIndex(r => r.id === rec.id)
            Object.assign(state.records.data[index], rec)
        },
        DELETE_RECORD(state, id){
            const index = state.records.data.findIndex(rec => rec.id === id)
            state.records.data.splice(index, 1)
        },
    },
    state: {
        records: {},
    },
    getters: {
        allRecords(state){
            return state.records
        },
        recordById: state => id => {
            return state.records.data.find(rec => rec.id === id)
        },
        countRecords: state => {
            return state.records.total
        },
    },
}
