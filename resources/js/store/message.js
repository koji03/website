const state = {
    content: ''
  }
  
//上にメッセージバーを表示する。
const mutations = {
    setContent (state, { content, timeout }) {
        state.content = content

        if (typeof timeout === 'undefined') {
            timeout = 3000
        }

        setTimeout(() => (state.content = ''), timeout)
    }
}

export default {
    namespaced: true,
    state,
    mutations
}