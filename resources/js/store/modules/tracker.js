export default {
    actions: {
        async getTrackerData({commit}){
            await axios.get('/api/reportTracker')
                .then(response => {
                    commit('UPDATE_TRACKER_DATA', response.data)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        }
    },
    mutations: {
        UPDATE_TRACKER_DATA(state, data){
            state.data = data
        }
    },
    state: {
        data: []
    },
    getters: {
        trackerData(state){
            return state.data
        }
    },
}
