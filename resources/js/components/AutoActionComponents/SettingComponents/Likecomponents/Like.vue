<template>
    <div class="c-setting__border">
        <div class="c-setting__wrap">
            <LikeButton/>
            <LikeForm/>
        </div>
    </div>
</template>
<script>
import LikeForm from './LikeParts/LikeForm'
import LikeButton from './LikeParts/LikeButton'

import { OK } from '../../../../util'
import { mapState } from 'vuex'
export default {
    
    data(){
        return{
        }
    },
    components:{
        LikeForm,
        LikeButton
    },
    methods:{
        //サーチ用のワードリストをDBから取得する。
        async getLikeWordList(){
            this.$store.commit('autoActionSetting/ResetLikeWordList')
            this.$store.commit('autoActionSetting/setLikeLoad',true)
        //通信中はロード画面を出す
            const response = await axios.post('/api/getLikeWordList',{
                twitter_screen_name:this.twitter_screen_name
            });
            if(response.status !== OK){
                this.$store.commit('twitter/setLikeLoad',false)
                this.$store.commit('error/setCode',response.status)
                return false
            }
            await response.data.forEach(element => {
                this.$store.dispatch('autoActionSetting/setLikeWordList',element)
            });
            this.$store.commit('autoActionSetting/setLikeLoad',false)
        },
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    },
    created(){
        this.getLikeWordList()
    },
    watch:{
        '$route':function(){
            this.getLikeWordList()
        }
    }
}
</script>