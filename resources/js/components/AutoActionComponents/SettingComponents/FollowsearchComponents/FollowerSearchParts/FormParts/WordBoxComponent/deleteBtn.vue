<template>
    <i class="fas fa-trash p-search__list__trash" @click="deleteWord"></i>
</template>

<script>
import { OK } from '../../../../../../../util'
import { mapState } from 'vuex'
export default {
    props:{
        word:{
            type: [String, Number],
            required:true
        },
        type:{
            required:true
        }
    },
    methods:{
        //ワード削除
        async deleteWord(){
            if(!confirm(this.word+'を削除しますか？')){
                return false
            }
            this.$store.commit('autoActionSetting/setFollowerSearchLoad',true)
            const response = await axios.post('/api/deleteSerachWord',{
                twitter_screen_name:this.twitter_screen_name,
                type:this.type,
                word:this.word
            })
            if(response.status !== OK){
                this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
                this.$store.commit('autoActionSetting/setFollowerSearchError','削除に失敗しました。ブラウザを更新後もう一度お試しください。')
            }
            this.$store.commit('autoActionSetting/delWordList',
            {
                word:this.word,
                type:this.type
            }
            )
            this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
            
        }
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    }
}
</script>