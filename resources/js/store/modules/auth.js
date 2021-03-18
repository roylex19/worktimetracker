export default {
    namespaced: true,
    actions: {
        async signIn(context, data){
            await axios.post('/api/auth/signIn', data).then(response => {
                return context.dispatch('attempt', response.data.token)
            })
        },
        async attempt(context, token){
            if(token){
                context.commit('SET_TOKEN', token)
            }

            if(!context.state.token){
                return
            }

            try{
                await axios.get('/api/auth/me').then(response => {
                    context.commit('SET_USER', response.data)
                })
            }catch (e) {
                context.commit('SET_TOKEN', null)
                context.commit('SET_USER', null)
            }
        },
        async signOut(context){
            return await axios.post('/api/auth/signOut').then(response => {
                context.commit('SET_TOKEN', null)
                context.commit('SET_USER', null)
            })
        },
        async register(context, data){
            await axios.post('/api/auth/register', data).then(response => {
                Vue.$toast.open({
                    message: response.data.message,
                    type: response.data.type,
                })
            }).catch((e) => {
                Vue.$toast.open({
                    message: e.name + ": " + e.message,
                    type: 'error',
                })
            })
        },
    },
    mutations: {
        SET_TOKEN(state, token){
            state.token = token
        },
        SET_USER(state, data){
            state.user = data
        }
    },
    state: {
        token: null,
        user: null
    },
    getters: {
        auth(state){
            return state.token && state.user
        },
        user(state){
            return state.user
        }
    },
}
